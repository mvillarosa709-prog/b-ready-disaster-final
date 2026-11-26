<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResendInvitationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:send-invitation {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send or re-send the live B-READY invitation email with credentials to a user via Brevo';

    /**
     * Execute the console command.
     */
    public function handle(BrevoMailService $brevoMailService): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User not found with email: {$email}");
            return Command::FAILURE;
        }

        $password = $this->argument('password') ?? 'BReady2026!';
        
        $user->forceFill([
            'password' => Hash::make($password),
            'must_change_password' => true,
        ])->save();

        $loginUrl = route('login');

        $this->info("Sending invitation email to {$user->name} ({$user->email})...");
        $this->info("Login URL: {$loginUrl}");
        $this->info("Temporary Password: {$password}");

        $result = $brevoMailService->sendInvitation($user, $password, $loginUrl);

        if ($result['success']) {
            $this->info("✓ SUCCESS ({$result['method']}): {$result['message']}");
            return Command::SUCCESS;
        }

        $this->error("✗ FAILED: {$result['message']}");
        return Command::FAILURE;
    }
}
