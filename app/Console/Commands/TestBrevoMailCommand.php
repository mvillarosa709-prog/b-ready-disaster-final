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

        $tempUser = new User([
            'name' => 'Brevo Test User',
            'email' => $recipientEmail,
            'role' => 'teacher',
        ]);

        $result = $brevoService->sendInvitation($tempUser, 'TestBreadyPass123!', url('/login'));

        if ($result['success']) {
            $this->info("✓ SUCCESS ({$result['method']}): {$result['message']}");
            return Command::SUCCESS;
        }

        $this->error("✗ FAILED: {$result['message']}");
        return Command::FAILURE;
    }
}
