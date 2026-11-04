<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['trainer', 'teacher', 'admin'])->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'admin')->update(['role' => 'trainer']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['trainer', 'teacher'])->change();
        });
    }
};
