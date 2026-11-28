<?php

use App\Models\ClassroomMaterial;
use App\Models\ClassroomPackage;
use App\Models\Course;
use App\Models\Material;
use App\Models\Module;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopTeacher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Publish all Courses so all modules and workshops are visible to teachers
        if (Schema::hasTable('courses')) {
            Course::query()->update(['status' => 'published']);
        }

        // 2. Publish all Workshops so teachers can discover and participate
        if (Schema::hasTable('workshops')) {
            Workshop::where('status', 'draft')->update(['status' => 'published']);
        }

        // 3. Normalize WorkshopTeacher enrollments so 'enrolled' is treated as 'registered'
        if (Schema::hasTable('workshop_teachers')) {
            WorkshopTeacher::where('status', 'enrolled')->update(['status' => 'registered']);

            // Ensure demo and main teacher accounts have active registered access to Workshop 1 & 3
            $teacherEmails = ['teacherbready@gmail.com', 'teacher@example.com'];
            foreach ($teacherEmails as $email) {
                $teacher = User::where('email', $email)->first();
                if ($teacher) {
                    foreach ([1, 3] as $workshopId) {
                        if (Workshop::where('id', $workshopId)->exists()) {
                            WorkshopTeacher::updateOrCreate(
                                [
                                    'workshop_id' => $workshopId,
                                    'teacher_id' => $teacher->id,
                                ],
                                [
                                    'user_id' => $teacher->id,
                                    'status' => 'registered',
                                    'joined_at' => now(),
                                ]
                            );
                        }
                    }
                }
            }
        }

        // 4. Link PowerPoint Presentation (chest-compression-only-cpr.pptx) to Modules
        if (Schema::hasTable('materials') && Schema::hasTable('modules')) {
            $filePath = 'materials/chest-compression-only-cpr.pptx';

            // Course 1 Module 1 (First module - immediately visible when opening Workshop 1)
            $mod1 = Module::where('course_id', 1)->where('sequence', 1)->first()
                ?? Module::where('title', 'like', '%Fundamentals of Seismic Hazards%')->first();
            if ($mod1) {
                Material::updateOrCreate(
                    [
                        'module_id' => $mod1->id,
                        'title' => 'Fire Drill & CPR Simulation Presentation (PPTX)',
                    ],
                    [
                        'type' => 'presentation',
                        'file_path' => $filePath,
                        'description' => 'Hands-only CPR and emergency fire drill simulation presentation slide deck. Covers chest compression depth (2-2.4 inches), 100-120 bpm rate, and classroom drill procedures.',
                    ]
                );
            }

            // Course 1 Module 2 (Evacuation Drills & Incident Command)
            $mod2 = Module::where('course_id', 1)->where('sequence', 2)->first()
                ?? Module::where('title', 'like', '%Evacuation Drills%')->first();
            if ($mod2) {
                Material::updateOrCreate(
                    [
                        'module_id' => $mod2->id,
                        'title' => 'Fire Drill Bucket Relay Simulation (PPTX Presentation)',
                    ],
                    [
                        'type' => 'presentation',
                        'file_path' => $filePath,
                        'description' => 'FIRE DRILL BUCKET RELAY: A community-based emergency response exercise simulating firefighting using improvised water transfer. Promotes coordination, teamwork, and bayanihan.',
                    ]
                );
            }

            // Course 3 Module 2 (Module 8: Fire Extinguisher & Suppression Drills)
            $modFire = Module::where('course_id', 3)->where('sequence', 2)->first()
                ?? Module::where('title', 'like', '%Fire Extinguisher%')->first();
            if ($modFire) {
                Material::updateOrCreate(
                    [
                        'module_id' => $modFire->id,
                        'title' => 'Fire Drill Bucket Relay Simulation (PPTX Presentation)',
                    ],
                    [
                        'type' => 'presentation',
                        'file_path' => $filePath,
                        'description' => 'FIRE DRILL BUCKET RELAY: A community-based emergency response exercise simulating firefighting using improvised water transfer. Promotes coordination, teamwork, and bayanihan.',
                    ]
                );
            }
        }

        // 5. Link PowerPoint Presentation to Classroom Packages
        if (Schema::hasTable('classroom_materials') && Schema::hasTable('classroom_packages')) {
            $pkgFilePath = 'classroom_packages/chest-compression-only-cpr.pptx';

            foreach ([1, 3] as $pkgId) {
                $pkg = ClassroomPackage::find($pkgId);
                if ($pkg) {
                    ClassroomMaterial::updateOrCreate(
                        [
                            'classroom_package_id' => $pkg->id,
                            'title' => 'Fire Drill & CPR Simulation (Teacher Presentation Slide Deck)',
                        ],
                        [
                            'material_type' => 'classroom_activity',
                            'file_path' => $pkgFilePath,
                            'original_filename' => 'chest-compression-only-cpr.pptx',
                            'description' => 'Interactive PowerPoint slide deck (.pptx) for teachers to facilitate CPR simulations and fire evacuation drills.',
                            'sequence' => 99,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Material::where('title', 'Fire Drill & CPR Simulation Presentation (PPTX)')->delete();
        ClassroomMaterial::where('title', 'Fire Drill & CPR Simulation (Teacher Presentation Slide Deck)')->delete();
    }
};
