<?php

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
        $title = 'Save Your Breath, Save a Life: Hands-Only CPR & Emergency Simulation (PPTX Presentation)';
        $desc = 'Official Disaster Preparedness & Basic Life Support (BLS) training presentation based on AO 155 s. 2004 and RA 10871 (BLS in Schools Act). Covers 100-120 bpm chest compressions, 2-2.4 in depth, B.E. F.A.S.T. stroke protocol, and emergency alerting.';

        // Attach to Module 1, Module 2, Module 4, Module 8
        $modules = Module::whereIn('id', [1, 2, 4, 8])->get();
        foreach ($modules as $module) {
            Material::updateOrCreate(
                [
                    'module_id' => $module->id,
                    'file_path' => $filePath,
                ],
                [
                    'title' => $title,
                    'type' => 'presentation',
                    'description' => $desc,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Material::where('title', 'like', '%Save Your Breath, Save a Life%')->delete();
    }
};
