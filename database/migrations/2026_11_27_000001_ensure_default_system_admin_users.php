<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $allPermissions = array_keys(User::allAvailablePermissions());

        // Primary Super Admin accounts (ensures teammates can log in under any standard admin email)
        $adminAccounts = [
            [
                'email' => 'adminbready@gmail.com',
                'name' => 'Super Administrator',
                'password' => 'adminbready123!',
            ],
            [
                'email' => 'admin@gmail.com',
                'name' => 'System Admin',
                'password' => 'adminbready123!',
            ],
            [
                'email' => 'admin@example.com',
                'name' => 'Platform Administrator',
                'password' => 'adminbready123!',
            ],
            [
                'email' => 'mvillarosa709@gmail.com',
                'name' => 'Lead Administrator (Michelle)',
                'password' => 'adminbready123!',
            ],
        ];

        foreach ($adminAccounts as $account) {
            $existing = User::where('email', $account['email'])->first();
            if ($existing) {
                $existing->update([
                    'role' => 'admin',
                    'permissions' => $allPermissions,
                    'is_active' => true,
                ]);
            } else {
                User::create([
                    'name' => $account['name'],
                    'email' => $account['email'],
                    'password' => Hash::make($account['password']),
                    'role' => 'admin',
                    'permissions' => $allPermissions,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'must_change_password' => false,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep user accounts
    }
};
