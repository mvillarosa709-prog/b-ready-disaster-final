<?php

use App\Models\Course;
use App\Models\Material;
use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('materials') || ! Schema::hasTable('modules')) {
            return;
        }

        $filePath = 'materials/chest-compression-only-cpr.pptx';

        // 1. Attach to Course 1 Module 2 (Active NCR Teachers Cohort Workshop)
        $modEvac = Module::where('title', 'like', '%Evacuation Drills%')->first();
        if ($modEvac) {
            Material::updateOrCreate(
                [
                    'module_id' => $modEvac->id,
                    'title' => 'Fire Drill Bucket Relay Simulation (PPTX Presentation)',
                ],
                [
                    'type' => 'presentation',
                    'file_path' => $filePath,
                    'description' => 'FIRE DRILL BUCKET RELAY: A community-based emergency response exercise simulating firefighting using improvised water transfer. Promotes coordination, teamwork, and bayanihan.',
                ]
            );
        }

        // 2. Attach to Course 3 Module 2 (Fire Extinguisher & Suppression Drills)
        $modFire = Module::where('title', 'like', '%Fire Extinguisher%')->first();
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Material::where('title', 'Fire Drill Bucket Relay Simulation (PPTX Presentation)')->delete();
    }
};
