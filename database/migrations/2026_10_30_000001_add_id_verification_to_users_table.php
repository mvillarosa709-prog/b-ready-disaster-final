<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_number')->nullable()->after('role');
            $table->string('id_file_path')->nullable()->after('id_number');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                  ->default('approved') // default approved para magamit sa demo agad, pero naka-record ang ID
                  ->after('id_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_number', 'id_file_path', 'verification_status']);
        });
    }
};
