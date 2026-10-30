<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MaterialController;
use App\Models\Question;
use App\Models\Choice;
use App\Models\TeacherAssessmentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'trainer'){
        return redirect()->route('trainer.dashboard');
    }

    return redirect()->route('teacher.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:trainer'])->group(function () {
    Route::get('/trainer/dashboard', [TrainerController::class, 'dashboard'])->name('trainer.dashboard');

    // Trainer Courses Management
    Route::get('/trainer/courses', [CourseController::class, 'index'])->name('trainer.courses.index');
    Route::get('/trainer/courses/create', [CourseController::class, 'create'])->name('trainer.courses.create');
    Route::post('/trainer/courses', [CourseController::class, 'store'])->name('trainer.courses.store');
    Route::get('/trainer/courses/{id}', [CourseController::class, 'show'])->name('trainer.courses.show');
    Route::get('/trainer/courses/{id}/edit', [CourseController::class, 'edit'])->name('trainer.courses.edit');
    Route::put('/trainer/courses/{id}', [CourseController::class, 'update'])->name('trainer.courses.update');
    Route::delete('/trainer/courses/{id}', [CourseController::class, 'destroy'])->name('trainer.courses.destroy');

    // Trainer Modules Management
    Route::get('/trainer/courses/{courseId}/modules/create', [ModuleController::class, 'create'])->name('trainer.modules.create');
    Route::post('/trainer/courses/{courseId}/modules', [ModuleController::class, 'store'])->name('trainer.modules.store');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/edit', [ModuleController::class, 'edit'])->name('trainer.modules.edit');
    Route::put('/trainer/courses/{courseId}/modules/{moduleId}', [ModuleController::class, 'update'])->name('trainer.modules.update');
    Route::delete('/trainer/courses/{courseId}/modules/{moduleId}', [ModuleController::class, 'destroy'])->name('trainer.modules.destroy');

    // Trainer Materials Management
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/materials/create', [MaterialController::class, 'create'])->name('trainer.materials.create');
    Route::post('/trainer/courses/{courseId}/modules/{moduleId}/materials', [MaterialController::class, 'store'])->name('trainer.materials.store');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}/edit', [MaterialController::class, 'edit'])->name('trainer.materials.edit');
    Route::put('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}', [MaterialController::class, 'update'])->name('trainer.materials.update');
    Route::delete('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}', [MaterialController::class, 'destroy'])->name('trainer.materials.destroy');

    // Trainer Assessment Features (Uploader, Take Assessment, Results, and Reports)
    Route::get('/trainer/assessments/create', function () {
        return view('trainer.create');
    })->name('trainer.assessments.create');

    Route::post('/trainer/assessments/store', function (Request $request) {
        if ($request->hasFile('assessment_file')) {
            $file = $request->file('assessment_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $sequenceNumber = 1;

            foreach ($data as $index => $row) {
                if ($index === 0 || empty($row[0])) continue;

                $question = new Question();
                $question->assessment_id = 1;
                $question->question_text = trim($row[0]);
                $question->sequence = $sequenceNumber++;
                $question->save();

                for ($i = 1; $i <= 4; $i++) {
                    if (isset($row[$i]) && trim($row[$i]) !== '') {
                        $isCorrect = (isset($row[5]) && trim($row[5]) == trim($row[$i])) ? 1 : 0;

                        $choice = new Choice();
                        $choice->question_id = $question->id;
                        $choice->choice_text = trim($row[$i]);
                        $choice->is_correct = $isCorrect;
                        $choice->sequence = $i;
                        $choice->save();
                    }
                }
            }

            return redirect('/trainer/assessments')->with('success', 'Questions uploaded successfully.');
        }

        return back()->with('error', 'Please upload a valid CSV file.');
    })->name('trainer.assessments.store');

    Route::get('/trainer/assessments', function () {
        $questions = Question::with('choices')->get();
        return view('trainer.index', compact('questions'));
    })->name('trainer.assessments.index');

    Route::get('/trainer/take-assessment', function () {
        $questions = Question::with('choices')->get();
        return view('trainer.take-assessment', compact('questions'));
    })->name('trainer.assessments.take');

    Route::post('/trainer/grade-assessment', function (Request $request) {
        $answers = $request->input('answers', []);
        $score = 0;
        $totalQuestions = count($answers);
        $detailedResults = [];

        foreach ($answers as $questionId => $choiceId) {
            $choice = Choice::find($choiceId);
            $question = Question::find($questionId);

            $isCorrect = $choice && $choice->is_correct;
            if ($isCorrect) {
                $score++;
            }

            $detailedResults[] = [
                'question' => $question->question_text ?? '',
                'selected_choice' => $choice->choice_text ?? '',
                'is_correct' => $isCorrect
            ];
        }

        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        $isPassed = $percentage >= 75;

        TeacherAssessmentResult::create([
            'teacher_name' => auth()->user()->name ?? 'Trainer / Teacher',
            'score' => $score,
            'total_questions' => $totalQuestions,
            'is_passed' => $isPassed,
        ]);

        return view('trainer.score-result', compact('score', 'totalQuestions', 'detailedResults', 'isPassed'));
    })->name('trainer.assessments.grade');

    Route::get('/trainer/reports', function () {
        $results = TeacherAssessmentResult::all();
        $totalTakers = $results->count();
        $passedCount = $results->where('is_passed', true)->count();
        $passingRate = $totalTakers > 0 ? round(($passedCount / $totalTakers) * 100, 1) : 0;

        return view('trainer.reports', compact('results', 'totalTakers', 'passedCount', 'passingRate'));
    })->name('trainer.reports');
});

Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

    // Personal Reports Dashboard (Aggregating Progress, Assessments, Certifications, Implementations)
    Route::get('/reports', [\App\Http\Controllers\Teacher\ReportController::class, 'index'])->name('reports.index');

    // Workshop Discovery & Registration
    Route::get('/workshops', [\App\Http\Controllers\Teacher\WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/my-workshops', [\App\Http\Controllers\Teacher\WorkshopController::class, 'myWorkshops'])->name('workshops.my');
    Route::get('/workshops/{workshop}', [\App\Http\Controllers\Teacher\WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/join', [\App\Http\Controllers\Teacher\WorkshopController::class, 'join'])->name('workshops.join');
    Route::post('/workshops/{workshop}/register', [\App\Http\Controllers\Teacher\WorkshopController::class, 'join'])->name('workshops.register');
    Route::post('/workshops/{workshop}/enroll', [\App\Http\Controllers\Teacher\WorkshopController::class, 'join'])->name('workshops.enroll');

    // Sequential Learning & Modules
    Route::get('/workshops/{workshop}/modules/{module}', [\App\Http\Controllers\Teacher\LearningController::class, 'show'])->name('learning.module');
    Route::post('/workshops/{workshop}/modules/{module}/complete', [\App\Http\Controllers\Teacher\LearningController::class, 'complete'])->name('learning.module.complete');
    Route::get('/workshops/{workshop}/modules/{module}/materials/{material}', [\App\Http\Controllers\Teacher\LearningController::class, 'downloadMaterial'])->name('learning.material.download');
    Route::post('/workshops/{workshop}/modules/{module}/materials/{material}/interact', [\App\Http\Controllers\Teacher\LearningController::class, 'interactMaterial'])->name('learning.material.interact');

    // Assessments & Certification
    Route::get('/workshops/{workshop}/assessment', [\App\Http\Controllers\Teacher\AssessmentController::class, 'show'])->name('assessments.show');
    Route::post('/workshops/{workshop}/assessment', [\App\Http\Controllers\Teacher\AssessmentController::class, 'submit'])->name('assessments.submit');
    Route::get('/workshops/{workshop}/assessment/attempts/{attempt}', [\App\Http\Controllers\Teacher\AssessmentController::class, 'result'])->name('assessments.result');
    Route::get('/workshops/{workshop}/certificate', [\App\Http\Controllers\Teacher\AssessmentController::class, 'certificate'])->name('assessments.certificate');

    // Classroom Packages (Guarded: must be certified to download/preview)
    Route::get('/packages', [\App\Http\Controllers\Teacher\ClassroomPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{package}', [\App\Http\Controllers\Teacher\ClassroomPackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{package}/materials/{material}', [\App\Http\Controllers\Teacher\ClassroomPackageController::class, 'downloadMaterial'])->middleware('certified.teacher')->name('packages.material.download');
    Route::get('/packages/{package}/materials/{material}/preview', [\App\Http\Controllers\Teacher\ClassroomPackageController::class, 'previewMaterial'])->middleware('certified.teacher')->name('packages.material.preview');

    // Classroom Implementation & Student Results (Guarded: must be certified to conduct implementations)
    Route::get('/implementations', [\App\Http\Controllers\Teacher\ImplementationController::class, 'index'])->name('implementations.index');
    Route::get('/implementations/create', [\App\Http\Controllers\Teacher\ImplementationController::class, 'create'])->name('implementations.create');
    Route::post('/implementations', [\App\Http\Controllers\Teacher\ImplementationController::class, 'store'])->middleware('certified.teacher')->name('implementations.store');
    Route::get('/implementations/{implementation}', [\App\Http\Controllers\Teacher\ImplementationController::class, 'show'])->name('implementations.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
