<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\ClassroomImplementation;
use App\Models\ClassroomMaterial;
use App\Models\ClassroomPackage;
use App\Models\ModuleProgress;
use App\Models\StudentResult;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TeacherPackageDemoSeeder extends Seeder
{
    /**
     * Populate realistic Classroom Packages, real PDF files, and Teacher unlock/implementation demo data
     * without removing any user-created courses or workshops.
     */
    public function run(): void
    {
        $trainer = User::where('role', 'trainer')->first()
            ?? User::where('email', 'trainer@example.com')->first();

        if (! $trainer) {
            return;
        }

        // 1. Ensure downloadable PDF files exist for existing & new classroom packages
        $pdfFiles = [
            'classroom_packages/student_guide.pdf' => [
                'Student DRR Activity Guidebook',
                'Printable guide for students covering earthquake safety, go-bag preparation, and family evacuation plans.',
            ],
            'classroom_packages/teacher_protocol.pdf' => [
                'Facilitator Emergency Drill Protocol & Rubric',
                'Step-by-step teacher facilitation checklist and scoring rubric for school earthquake evacuation drills.',
            ],
            'classroom_packages/worksheet.pdf' => [
                'Hazard Hunt Classroom Activity Worksheet',
                'Interactive classroom activity sheet for identifying falling hazards and safe zones inside school rooms.',
            ],
            'classroom_packages/assessment_key.pdf' => [
                'Post-Drill Student Assessment & Answer Key',
                'Short student readiness quiz and teacher answer key for classroom DRR evaluation.',
            ],
            'classroom_packages/family_gobag.pdf' => [
                'Family Disaster Go-Bag Activity Booklet',
                'Typhoon & flood readiness activity workbook and emergency supply checklist for households.',
            ],
            'classroom_packages/fire_safety_kit.pdf' => [
                'Campus Fire Safety & P.A.S.S. Drill Kit',
                'Classroom fire prevention lesson guide, extinguisher P.A.S.S. steps, and lab safety checklist.',
            ],
            'classroom_packages/fvgg_classroom_kit.pdf' => [
                'Workshop Classroom Implementation Guide',
                'Official classroom toolkit containing facilitator instructions, student exercises, and drill assessment rubric.',
            ],
        ];

        foreach ($pdfFiles as $path => [$heading, $body]) {
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $this->buildMinimalPdf($heading, $body));
            }
        }

        // 2. Ensure every published/ongoing workshop has a Classroom Package with materials
        $workshops = Workshop::whereIn('status', ['published', 'ongoing'])
            ->with(['course.modules.quiz', 'classroomPackage.materials'])
            ->get();

        foreach ($workshops as $workshop) {
            $package = ClassroomPackage::firstOrCreate(
                ['workshop_id' => $workshop->id],
                [
                    'trainer_id' => $workshop->trainer_id ?? $trainer->id,
                    'title' => $workshop->title . ' — Classroom Toolkit',
                    'description' => 'Complete teaching kit containing facilitator guides, student manuals, classroom worksheets, and evaluation rubrics.',
                    'is_published' => true,
                ]
            );

            if ($package->materials()->count() === 0) {
                $defaultPath = str_contains(strtolower($workshop->title), 'fire')
                    ? 'classroom_packages/fire_safety_kit.pdf'
                    : 'classroom_packages/fvgg_classroom_kit.pdf';

                ClassroomMaterial::create([
                    'classroom_package_id' => $package->id,
                    'title' => 'Facilitator Guide & Lesson Plan (PDF)',
                    'material_type' => 'teacher_guide',
                    'file_path' => $defaultPath,
                    'original_filename' => 'Facilitator_Lesson_Plan.pdf',
                    'description' => 'Ready-to-teach classroom guide for rolling out this DRR topic with students.',
                    'sequence' => 1,
                ]);

                ClassroomMaterial::create([
                    'classroom_package_id' => $package->id,
                    'title' => 'Student Drill Worksheet & Activity Sheet (PDF)',
                    'material_type' => 'worksheet',
                    'file_path' => 'classroom_packages/worksheet.pdf',
                    'original_filename' => 'Student_Drill_Worksheet.pdf',
                    'description' => 'Printable student activity sheet and readiness check.',
                    'sequence' => 2,
                ]);

                ClassroomMaterial::create([
                    'classroom_package_id' => $package->id,
                    'title' => 'Student Assessment & Answer Key (PDF)',
                    'material_type' => 'answer_key',
                    'file_path' => 'classroom_packages/assessment_key.pdf',
                    'original_filename' => 'Assessment_Answer_Key.pdf',
                    'description' => 'Evaluation form and answer key for post-session student scoring.',
                    'sequence' => 3,
                ]);
            }
        }

        // 3. Seed demo teacher accounts so Maria Santos (and active teacher accounts) see both Unlocked & Locked packages
        $demoTeachers = User::where('role', 'teacher')
            ->whereIn('email', ['teacher@example.com', 'mvillarosa709@gmail.com', 'm00850555@gmail.com'])
            ->get();

        $unlockedWorkshop = Workshop::whereIn('status', ['published', 'ongoing'])
            ->whereHas('classroomPackage.materials')
            ->orderBy('id')
            ->first();

        $lockedWorkshop = Workshop::whereIn('status', ['published', 'ongoing'])
            ->whereHas('classroomPackage')
            ->when($unlockedWorkshop, fn ($q) => $q->where('id', '!=', $unlockedWorkshop->id))
            ->orderBy('id')
            ->first();

        if (! $unlockedWorkshop) {
            return;
        }

        $unlockedWorkshop->load('course.modules', 'classroomPackage');

        foreach ($demoTeachers as $teacher) {
            // A. Enroll + Complete + Certify (Approved) in Workshop 1 -> UNLOCKED Workshop Package
            WorkshopTeacher::updateOrCreate(
                [
                    'workshop_id' => $unlockedWorkshop->id,
                    'teacher_id' => $teacher->id,
                ],
                [
                    'user_id' => $teacher->id,
                    'status' => 'completed',
                    'joined_at' => now()->subDays(10),
                ]
            );

            $modules = $unlockedWorkshop->course?->modules ?? collect();
            foreach ($modules as $mod) {
                ModuleProgress::updateOrCreate(
                    [
                        'workshop_id' => $unlockedWorkshop->id,
                        'module_id' => $mod->id,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'user_id' => $teacher->id,
                        'progress' => 100,
                        'status' => 'completed',
                        'started_at' => now()->subDays(9),
                        'completed_at' => now()->subDays(5),
                    ]
                );
            }

            $assessment = Assessment::where('workshop_id', $unlockedWorkshop->id)->first()
                ?? ($modules->first() ? Assessment::where('module_id', $modules->first()->id)->first() : null);

            if (! $assessment) {
                $assessment = Assessment::create([
                    'workshop_id' => $unlockedWorkshop->id,
                    'module_id' => $modules->first()?->id,
                    'trainer_id' => $unlockedWorkshop->trainer_id ?? $trainer->id,
                    'title' => $unlockedWorkshop->title . ' — Competency Assessment',
                    'description' => 'Competency evaluation for DRR certification.',
                    'passing_score' => 75,
                    'time_limit' => 30,
                    'max_attempts' => 3,
                    'is_published' => true,
                ]);
            }

            $attempt = AssessmentAttempt::firstOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'workshop_id' => $unlockedWorkshop->id,
                    'teacher_id' => $teacher->id,
                    'attempt_number' => 1,
                ],
                [
                    'score' => 94.00,
                    'percentage' => 94.00,
                    'result' => 'passed',
                    'started_at' => now()->subDays(5),
                    'submitted_at' => now()->subDays(5)->addMinutes(12),
                ]
            );

            Certification::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'workshop_id' => $unlockedWorkshop->id,
                ],
                [
                    'assessment_attempt_id' => $attempt->id,
                    'certificate_number' => 'BRD-2026-T' . str_pad((string) $teacher->id, 3, '0', STR_PAD_LEFT) . 'DRR',
                    'badge_name' => 'DRR Certified Educator',
                    'certified_at' => now()->subDays(4),
                    'status' => 'approved',
                    'approved_by' => $trainer->id,
                    'approved_at' => now()->subDays(4),
                ]
            );

            // Sample Classroom Implementation for this unlocked package
            if ($unlockedWorkshop->classroomPackage && ClassroomImplementation::where('teacher_id', $teacher->id)->where('workshop_id', $unlockedWorkshop->id)->doesntExist()) {
                $implementation = ClassroomImplementation::create([
                    'teacher_id' => $teacher->id,
                    'workshop_id' => $unlockedWorkshop->id,
                    'classroom_package_id' => $unlockedWorkshop->classroomPackage->id,
                    'implementation_date' => now()->subDays(2),
                    'status' => 'completed',
                    'students_participated' => 32,
                    'students_completed_assessment' => 32,
                    'students_passed' => 29,
                    'students_failed' => 3,
                    'teacher_reflection' => 'Rolled out the classroom earthquake drill toolkit with Grade 7 learners. Students executed Duck-Cover-Hold quickly and completed the hazard hunt worksheet.',
                    'remarks' => 'Recommended for quarterly school-wide safety refresher.',
                ]);

                foreach ([
                    ['LRN-2026-01 (Dela Cruz, J.)', 92, 92, 'passed'],
                    ['LRN-2026-02 (Reyes, M.)', 88, 88, 'passed'],
                    ['LRN-2026-03 (Garcia, K.)', 84, 84, 'passed'],
                    ['LRN-2026-04 (Lim, R.)', 65, 65, 'failed'],
                    ['LRN-2026-05 (Tan, S.)', 96, 96, 'passed'],
                ] as [$identifier, $score, $pct, $res]) {
                    StudentResult::create([
                        'classroom_implementation_id' => $implementation->id,
                        'student_identifier' => $identifier,
                        'score' => $score,
                        'total_questions' => 100,
                        'percentage' => $pct,
                        'status' => $pct >= 70 ? 'Passed' : 'Failed',
                        'result' => $res,
                    ]);
                }
            }

            // B. Enroll (In Progress, not yet certified) in Workshop 2 -> LOCKED Workshop Package preview
            if ($lockedWorkshop) {
                WorkshopTeacher::firstOrCreate(
                    [
                        'workshop_id' => $lockedWorkshop->id,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'user_id' => $teacher->id,
                        'status' => 'registered',
                        'joined_at' => now()->subDay(),
                    ]
                );
            }
        }
    }

    /**
     * Generate a clean, readable 1-page PDF binary without external dependencies.
     */
    private function buildMinimalPdf(string $title, string $description): string
    {
        $escape = static fn (string $text): string => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $wrapped = explode("\n", wordwrap($description, 75, "\n"));

        $streamLines = [
            'BT',
            '/F1 18 Tf',
            '50 750 Td',
            '(' . $escape('B-READY DRR Classroom Package') . ') Tj',
            '0 -32 Td',
            '/F1 14 Tf',
            '(' . $escape($title) . ') Tj',
            '0 -26 Td',
            '/F1 11 Tf',
        ];

        foreach ($wrapped as $line) {
            $streamLines[] = '(' . $escape($line) . ') Tj';
            $streamLines[] = '0 -16 Td';
        }

        $streamLines[] = '0 -20 Td';
        $streamLines[] = '(' . $escape('Generated for certified DRR educators - RAFi B-READY Capstone.') . ') Tj';
        $streamLines[] = 'ET';

        $stream = implode("\n", $streamLines) . "\n";

        $objects = [
            1 => "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            2 => "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            3 => "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n",
            4 => "4 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n{$stream}endstream\nendobj\n",
            5 => "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $num => $obj) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        foreach ($objects as $num => $_) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$num]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }
}

