<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ModuleProgress;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use App\Notifications\CertificatePendingApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AssessmentEvaluationService
{
    /**
     * Evaluate and store a teacher's assessment attempt.
     * Automatic Scoring with Zero Manual Input Rule.
     *
     * @param  array<int, int>  $submittedAnswers  [question_id => choice_id]
     * @return array{attempt: AssessmentAttempt, certification: ?Certification, passed: bool, score: float, total_points: float, percentage: float, correct_answers: int, total_questions: int, remaining_attempts: int}
     *
     * @throws InvalidArgumentException
     */
    public function evaluateAttempt(
        User $teacher,
        Assessment $assessment,
        array $submittedAnswers,
        ?Workshop $workshop = null
    ): array {
        $workshop ??= $assessment->workshop;
        if (! $workshop) {
            throw new InvalidArgumentException('A workshop is required to evaluate a module quiz.');
        }

        $certificateCreated = false;
        $result = DB::transaction(function () use ($teacher, $assessment, $submittedAnswers, $workshop, &$certificateCreated) {
            // Count existing attempts for this teacher and assessment
            $attemptCount = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('workshop_id', $workshop->id)
                ->where(function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->count();

            if ($attemptCount >= $assessment->max_attempts) {
                throw new InvalidArgumentException("Maximum attempts ({$assessment->max_attempts}) reached for this assessment.");
            }

            $alreadyPassed = AssessmentAttempt::where('assessment_id', $assessment->id)
                ->where('workshop_id', $workshop->id)
                ->where(function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                })
                ->where('result', 'passed')
                ->exists();

            if ($alreadyPassed) {
                throw new InvalidArgumentException('This assessment has already been passed.');
            }

            // Load assessment questions and choices
            $questions = $assessment->questions()->with('choices')->get();
            $totalQuestions = $questions->count();

            $totalPoints = 0;
            $earnedPoints = 0;
            $correctAnswersCount = 0;
            $answersData = [];

            foreach ($questions as $question) {
                $totalPoints += (float) $question->points;
                $submittedAnswer = trim((string) ($submittedAnswers[$question->id] ?? ''));
                $isCorrect = false;
                $pointsEarned = 0;
                $choiceId = null;
                $answerText = null;

                if ($question->question_type === 'fill_blank') {
                    $answerText = $submittedAnswer;
                    $normalizedAnswer = $this->normalizeAnswer($submittedAnswer);
                    $isCorrect = $normalizedAnswer !== ''
                        && $question->choices
                            ->where('is_correct', true)
                            ->contains(fn ($choice) => $this->normalizeAnswer($choice->choice_text) === $normalizedAnswer);
                } elseif (ctype_digit($submittedAnswer)) {
                    $selectedChoice = $question->choices->first(
                        fn ($choice) => (int) $choice->id === (int) $submittedAnswer
                    );
                    if ($selectedChoice) {
                        $choiceId = $selectedChoice->id;
                        $answerText = $selectedChoice->choice_text;
                        $isCorrect = $selectedChoice->is_correct;
                    }
                }

                if ($isCorrect) {
                    $pointsEarned = (float) $question->points;
                    $earnedPoints += $pointsEarned;
                    $correctAnswersCount++;
                }

                $answersData[] = [
                    'question_id' => $question->id,
                    'choice_id' => $choiceId,
                    'answer_text' => $answerText,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ];
            }

            // Zero Manual Input Rule: Compute percentage score ((Correct / Total) * 100) or ((Earned / TotalPoints) * 100)
            if ($totalPoints > 0) {
                $percentage = round(($earnedPoints / $totalPoints) * 100, 2);
            } elseif ($totalQuestions > 0) {
                $percentage = round(($correctAnswersCount / $totalQuestions) * 100, 2);
            } else {
                $percentage = 0.00;
            }

            $passed = $percentage >= $assessment->passing_score;
            $result = $passed ? 'passed' : 'failed';
            $newAttemptNumber = $attemptCount + 1;
            $remainingAttempts = max(0, $assessment->max_attempts - $newAttemptNumber);

            // Create Attempt Record
            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'workshop_id' => $workshop->id,
                'teacher_id' => $teacher->id,
                'attempt_number' => $newAttemptNumber,
                'score' => $earnedPoints,
                'percentage' => $percentage,
                'result' => $result,
                'started_at' => now()->subMinutes(rand(10, 25)),
                'submitted_at' => now(),
            ]);

            // Save individual candidate answer entries
            foreach ($answersData as $answer) {
                AssessmentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $answer['question_id'],
                    'choice_id' => $answer['choice_id'],
                    'answer_text' => $answer['answer_text'],
                    'is_correct' => $answer['is_correct'],
                    'points_earned' => $answer['points_earned'],
                ]);
            }

            $certification = null;

            if ($passed) {
                $shouldIssueCertification = true;

                if ($assessment->module_id) {
                    $requiredModuleIds = $workshop->ordered_modules
                        ->where('is_required', true)
                        ->pluck('id')
                        ->map(fn ($id) => (int) $id)
                        ->all();

                    $moduleIndex = array_search((int) $assessment->module_id, $requiredModuleIds, true);
                    $isLastRequiredModule = $moduleIndex !== false
                        && $moduleIndex === count($requiredModuleIds) - 1;

                    $otherRequiredModuleIds = array_values(array_diff(
                        $requiredModuleIds,
                        [(int) $assessment->module_id]
                    ));

                    $completedRequiredModuleCount = $otherRequiredModuleIds !== []
                        ? ModuleProgress::where('workshop_id', $workshop->id)
                            ->where(function ($query) use ($teacher) {
                                $query->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                            })
                            ->whereIn('module_id', $otherRequiredModuleIds)
                            ->where('status', 'completed')
                            ->count()
                        : 0;

                    $shouldIssueCertification = $isLastRequiredModule
                        && $completedRequiredModuleCount === count($otherRequiredModuleIds);
                }

                if ($shouldIssueCertification) {
                    $certification = Certification::where(function ($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                    })
                        ->where('workshop_id', $workshop->id)
                        ->first();

                    if (! $certification) {
                        $certNumber = 'BRD-'.date('Y').'-'.strtoupper(Str::random(8));

                        $certification = Certification::create([
                            'teacher_id' => $teacher->id,
                            'workshop_id' => $workshop->id,
                            'assessment_attempt_id' => $attempt->id,
                            'certificate_number' => $certNumber,
                            'badge_name' => 'DRR Certified Educator',
                            'certificate_file' => null,
                            'certified_at' => now(),
                            'status' => 'pending',
                        ]);
                        $certificateCreated = true;
                    }

                    WorkshopTeacher::where('workshop_id', $workshop->id)
                        ->where(function ($q) use ($teacher) {
                            $q->where('teacher_id', $teacher->id)->orWhere('user_id', $teacher->id);
                        })
                        ->update(['status' => 'completed']);
                }
            }

            return [
                'attempt' => $attempt,
                'certification' => $certification,
                'passed' => $passed,
                'score' => (float) $earnedPoints,
                'total_points' => (float) $totalPoints,
                'percentage' => $percentage,
                'correct_answers' => $correctAnswersCount,
                'total_questions' => $totalQuestions,
                'remaining_attempts' => $remainingAttempts,
            ];
        });

        if ($certificateCreated && $result['certification']) {
            $recipients = User::whereIn('role', ['trainer', 'admin'])->get();
            Notification::send(
                $recipients,
                new CertificatePendingApproval($result['certification']->load(['teacher', 'workshop']))
            );
        }

        return $result;
    }

    private function normalizeAnswer(string $answer): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($answer)) ?? '');
    }
}
