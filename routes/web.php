<?php

use App\Http\Controllers\Admin\CourseOversightController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WorkshopOversightController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleQuizController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teacher\AssessmentController;
use App\Http\Controllers\Teacher\ClassroomPackageController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\ImplementationController;
use App\Http\Controllers\Teacher\LearningController;
use App\Http\Controllers\Teacher\ReportController;
use App\Http\Controllers\Teacher\WorkshopController;
use App\Http\Controllers\Trainer\CertificateApprovalController;
use App\Http\Controllers\Trainer\WorkshopController as TrainerWorkshopController;
use App\Http\Controllers\TrainerController;
use App\Models\AssessmentAttempt;
use App\Models\Certification;
use App\Models\TeacherAssessmentResult;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'trainer' => redirect()->route('trainer.dashboard'),
        default => redirect()->route('teacher.dashboard'),
    };
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->role === 'trainer') {
        return redirect()->route('trainer.dashboard');
    }

    return redirect()->route('teacher.dashboard');
})->middleware(['auth', 'trainer.password', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/trainer/change-password', function () {
        return view('auth.trainer-change-password');
    })->name('trainer.password.change');

    Route::put('/trainer/change-password', [PasswordController::class, 'update'])
        ->name('trainer.password.update');
});

Route::middleware(['auth', 'role:trainer', 'trainer.password', 'verified'])->group(function () {
    Route::get('/trainer/dashboard', [TrainerController::class, 'dashboard'])->name('trainer.dashboard');
    Route::resource('/trainer/workshops', TrainerWorkshopController::class)
        ->names('trainer.workshops');
    Route::post('/trainer/workshops/{workshop}/publish', [TrainerWorkshopController::class, 'publish'])
        ->name('trainer.workshops.publish');
    Route::post('/trainer/workshops/{workshop}/package', [TrainerWorkshopController::class, 'savePackage'])
        ->name('trainer.workshops.package.save');
    Route::post('/trainer/workshops/{workshop}/package/materials', [TrainerWorkshopController::class, 'storePackageMaterial'])
        ->name('trainer.workshops.package.materials.store');
    Route::delete('/trainer/workshops/{workshop}/package/materials/{material}', [TrainerWorkshopController::class, 'destroyPackageMaterial'])
        ->name('trainer.workshops.package.materials.destroy');

    // Trainer Courses Management
    Route::get('/trainer/courses', [CourseController::class, 'index'])->name('trainer.courses.index');
    Route::get('/trainer/courses/create', [CourseController::class, 'create'])->name('trainer.courses.create');
    Route::post('/trainer/courses', [CourseController::class, 'store'])->name('trainer.courses.store');
    Route::get('/trainer/courses/{id}', [CourseController::class, 'show'])->name('trainer.courses.show');
    Route::get('/trainer/courses/{id}/edit', [CourseController::class, 'edit'])->name('trainer.courses.edit');
    Route::put('/trainer/courses/{id}', [CourseController::class, 'update'])->name('trainer.courses.update');
    Route::post('/trainer/courses/{id}/publish', [CourseController::class, 'publish'])->name('trainer.courses.publish');
    Route::post('/trainer/courses/{id}/unpublish', [CourseController::class, 'unpublish'])->name('trainer.courses.unpublish');
    Route::delete('/trainer/courses/{id}', [CourseController::class, 'destroy'])->name('trainer.courses.destroy');

    // Trainer Modules Management
    Route::get('/trainer/courses/{courseId}/modules/create', [ModuleController::class, 'create'])->name('trainer.modules.create');
    Route::post('/trainer/courses/{courseId}/modules', [ModuleController::class, 'store'])->name('trainer.modules.store');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/edit', [ModuleController::class, 'edit'])->name('trainer.modules.edit');
    Route::put('/trainer/courses/{courseId}/modules/{moduleId}', [ModuleController::class, 'update'])->name('trainer.modules.update');
    Route::delete('/trainer/courses/{courseId}/modules/{moduleId}', [ModuleController::class, 'destroy'])->name('trainer.modules.destroy');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/quiz', [ModuleQuizController::class, 'edit'])->name('trainer.modules.quiz.edit');
    Route::put('/trainer/courses/{courseId}/modules/{moduleId}/quiz', [ModuleQuizController::class, 'update'])->name('trainer.modules.quiz.update');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/quiz/template', [ModuleQuizController::class, 'downloadTemplate'])->name('trainer.modules.quiz.template');
    Route::post('/trainer/courses/{courseId}/modules/{moduleId}/quiz/import', [ModuleQuizController::class, 'import'])->name('trainer.modules.quiz.import');

    // Trainer Materials Management
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/materials/create', [MaterialController::class, 'create'])->name('trainer.materials.create');
    Route::post('/trainer/courses/{courseId}/modules/{moduleId}/materials', [MaterialController::class, 'store'])->name('trainer.materials.store');
    Route::get('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}/edit', [MaterialController::class, 'edit'])->name('trainer.materials.edit');
    Route::put('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}', [MaterialController::class, 'update'])->name('trainer.materials.update');
    Route::delete('/trainer/courses/{courseId}/modules/{moduleId}/materials/{materialId}', [MaterialController::class, 'destroy'])->name('trainer.materials.destroy');

    Route::get('/trainer/reports', function () {
        $results = TeacherAssessmentResult::all();
        $totalTakers = $results->count();
        $passedCount = $results->where('is_passed', true)->count();
        $passingRate = $totalTakers > 0 ? round(($passedCount / $totalTakers) * 100, 1) : 0;
        $pendingCertificates = Certification::where('status', 'pending')
            ->with(['teacher', 'workshop'])
            ->latest('certified_at')
            ->get();
        $moduleQuizAttempts = AssessmentAttempt::whereHas('assessment.module.course', function ($query) {
            $query->where('trainer_id', auth()->id());
        })
            ->with([
                'teacher',
                'assessment.module.course',
                'answers.question.choices',
                'answers.choice',
            ])
            ->latest('submitted_at')
            ->get();

        return view('trainer.reports', compact(
            'results',
            'totalTakers',
            'passedCount',
            'passingRate',
            'pendingCertificates',
            'moduleQuizAttempts'
        ));
    })->name('trainer.reports');
});

Route::middleware(['auth', 'trainer.password', 'verified', 'role:trainer,admin'])->group(function () {
    Route::get('/trainer/reports/pending-certificates', [CertificateApprovalController::class, 'index'])
        ->name('trainer.certificates.pending');
    Route::post('/trainer/certificates/{certification}/approve', [CertificateApprovalController::class, 'approve'])
        ->name('trainer.certificates.approve');
});

Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Personal Reports Dashboard (Aggregating Progress, Assessments, Certifications, Implementations)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Workshop Discovery & Registration
    Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
    Route::get('/my-workshops', [WorkshopController::class, 'myWorkshops'])->name('workshops.my');
    Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');
    Route::post('/workshops/{workshop}/join', [WorkshopController::class, 'join'])->name('workshops.join');
    Route::post('/workshops/{workshop}/register', [WorkshopController::class, 'join'])->name('workshops.register');
    Route::post('/workshops/{workshop}/enroll', [WorkshopController::class, 'join'])->name('workshops.enroll');

    // Sequential Learning & Modules
    Route::get('/workshops/{workshop}/modules/{module}', [LearningController::class, 'show'])->name('learning.module');
    Route::post('/workshops/{workshop}/modules/{module}/complete', [LearningController::class, 'complete'])->name('learning.module.complete');
    Route::get('/workshops/{workshop}/modules/{module}/materials/{material}', [LearningController::class, 'downloadMaterial'])->name('learning.material.download');
    Route::post('/workshops/{workshop}/modules/{module}/materials/{material}/interact', [LearningController::class, 'interactMaterial'])->name('learning.material.interact');

    // Assessments & Certification
    Route::post('/workshops/{workshop}/modules/{module}/assessment', [AssessmentController::class, 'submitModule'])->name('learning.module.assessment.submit');
    Route::get('/workshops/{workshop}/assessment/attempts/{attempt}', [AssessmentController::class, 'result'])->name('assessments.result');
    Route::get('/workshops/{workshop}/certificate', [AssessmentController::class, 'certificate'])->name('assessments.certificate');

    // Classroom Packages (Guarded: must be certified to download/preview)
    Route::get('/packages', [ClassroomPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{package}', [ClassroomPackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{package}/materials/{material}', [ClassroomPackageController::class, 'downloadMaterial'])->middleware('certified.teacher')->name('packages.material.download');
    Route::get('/packages/{package}/materials/{material}/preview', [ClassroomPackageController::class, 'previewMaterial'])->middleware('certified.teacher')->name('packages.material.preview');

    // Classroom Implementation & Student Results (Guarded: must be certified to conduct implementations)
    Route::get('/implementations', [ImplementationController::class, 'index'])->name('implementations.index');
    Route::get('/implementations/create', [ImplementationController::class, 'create'])->name('implementations.create');
    Route::post('/implementations', [ImplementationController::class, 'store'])->middleware('certified.teacher')->name('implementations.store');
    Route::get('/implementations/{implementation}', [ImplementationController::class, 'show'])->name('implementations.show');
});

Route::middleware(['auth', 'trainer.password'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User & Role Access Management
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Permissions Matrix
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

    // Platform Oversight
    Route::get('/workshops', [WorkshopOversightController::class, 'index'])->name('workshops.index');
    Route::get('/workshops/{workshop}', [WorkshopOversightController::class, 'show'])->name('workshops.show');
    Route::get('/courses', [CourseOversightController::class, 'index'])->name('courses.index');
    Route::get('/certificates', [CertificateApprovalController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/{certification}/approve', [CertificateApprovalController::class, 'approve'])->name('certificates.approve');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
