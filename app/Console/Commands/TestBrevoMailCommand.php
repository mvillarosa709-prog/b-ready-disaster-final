<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Console\Command;

class TestBrevoMailCommand extends Command
{
    protected $signature = 'mail:test-brevo {email? : The recipient email address}';
    protected $description = 'Send a test invitation email via Brevo to verify SMTP or API configuration';

    public function handle(BrevoMailService $brevoService): int
    {
        $recipientEmail = $this->argument('email') ?? config('mail.from.address', 'mvillarosa709@gmail.com');
        $this->info("Initiating Brevo test email to: {$recipientEmail}...");

        $existingUser = User::where('email', $recipientEmail)->first();
        $tempUser = $existingUser ?? new User([
            'name' => 'Teacher',
            'email' => $recipientEmail,
            'role' => 'teacher',
        ]);

        $password = 'teacherbready123!';
        if ($existingUser) {
            $existingUser->password = \Illuminate\Support\Facades\Hash::make($password);
            $existingUser->must_change_password = true;
            $existingUser->save();
        }

        $result = $brevoService->sendInvitation($tempUser, $password, url('/login'));

        if ($result['success']) {
            $this->info("✓ SUCCESS ({$result['method']}): {$result['message']}");
            return Command::SUCCESS;
        }

        $this->error("✗ FAILED: {$result['message']}");
        return Command::FAILURE;
    }
}
