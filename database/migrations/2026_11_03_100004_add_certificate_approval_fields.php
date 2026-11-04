<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        DB::table('certifications')->update(['status' => 'approved']);

        foreach (DB::table('certifications')->get(['id', 'workshop_id', 'certified_at']) as $certification) {
            $trainerId = DB::table('workshops')
                ->where('id', $certification->workshop_id)
                ->value('trainer_id');

            DB::table('certifications')
                ->where('id', $certification->id)
                ->update([
                    'approved_by' => $trainerId,
                    'approved_at' => $certification->certified_at,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status', 'approved_at']);
        });
    }
};
