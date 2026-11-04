<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('workshop_id')->nullable()->change();
        });

        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->foreignId('workshop_id')
                ->nullable()
                ->after('assessment_id')
                ->constrained('workshops')
                ->cascadeOnDelete();

            $table->dropUnique('assessment_attempt_unique');
            $table->unique(
                ['assessment_id', 'workshop_id', 'teacher_id', 'attempt_number'],
                'assessment_attempt_workshop_unique'
            );
        });

        foreach (DB::table('assessments')->whereNotNull('workshop_id')->get(['id', 'workshop_id']) as $assessment) {
            DB::table('assessment_attempts')
                ->where('assessment_id', $assessment->id)
                ->whereNull('workshop_id')
                ->update(['workshop_id' => $assessment->workshop_id]);
        }

        $now = now();
        $modules = DB::table('modules')
            ->join('courses', 'courses.id', '=', 'modules.course_id')
            ->select('modules.id as module_id', 'courses.trainer_id', 'modules.title')
            ->get();

        foreach ($modules as $module) {
            $hasModuleQuiz = DB::table('assessments')
                ->whereNull('workshop_id')
                ->where('module_id', $module->module_id)
                ->exists();

            if ($hasModuleQuiz) {
                continue;
            }

            DB::table('assessments')->insert([
                'workshop_id' => null,
                'module_id' => $module->module_id,
                'trainer_id' => $module->trainer_id,
                'title' => 'Module Quiz: '.$module->title,
                'description' => 'Add quiz questions and publish this quiz before publishing the course.',
                'passing_score' => 75,
                'time_limit' => 30,
                'max_attempts' => 3,
                'is_published' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('courses')
                ->join('modules', 'modules.course_id', '=', 'courses.id')
                ->where('modules.id', $module->module_id)
                ->where('courses.status', 'published')
                ->update(['courses.status' => 'draft']);
        }

        DB::table('courses')
            ->where('status', 'published')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('modules')
                    ->whereColumn('modules.course_id', 'courses.id');
            })
            ->update(['status' => 'draft']);
    }

    public function down(): void
    {
        DB::table('assessments')->whereNull('workshop_id')->delete();

        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->dropUnique('assessment_attempt_workshop_unique');
            $table->dropConstrainedForeignId('workshop_id');
            $table->unique(
                ['assessment_id', 'teacher_id', 'attempt_number'],
                'assessment_attempt_unique'
            );
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->foreignId('workshop_id')->nullable(false)->change();
        });
    }
};
