<?php

namespace App\Services;

use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BrevoMailService
{
    public const BREVO_SMTP_HOST = 'smtp-relay.brevo.com';
    public const BREVO_SMTP_PORT = 587;
    public const BREVO_SMTP_LOGIN = 'bd82b5001@smtp-brevo.com';
    public const BREVO_SMTP_FALLBACK = 'eHNtdHBzaWItYzljMjhmODAyMDdkMWEzOTkwOGM1OGRhYTNmYmFlMzc2ZGZlMGNhNTk3YjE1NmM2YzI3YWU3MjBiODhhNDExMS1MREp6VWxPdHZFV3Ixc0Ro';

    /**
     * Send user invitation email via Brevo API or SMTP.
     *
     * @return array{success: bool, message: string, method: string}
     */
    public function sendInvitation(User $user, string $plainPassword, string $loginUrl): array
    {
        $brevoApiKey = config('services.brevo.api_key', env('BREVO_API_KEY'));

        // 1. If Brevo REST API Key is configured (starts with xkeysib-), prioritize sending via Brevo v3 API directly
        if (! empty($brevoApiKey) && str_starts_with($brevoApiKey, 'xkeysib-')) {
            $apiResult = $this->sendViaBrevoApi($user, $plainPassword, $loginUrl, $brevoApiKey);
            if ($apiResult['success']) {
                return $apiResult;
            }
            Log::warning('Brevo API delivery failed, falling back to SMTP: ' . $apiResult['message']);
        }

        // 2. Ensure Brevo SMTP credentials are armed even if local .env is unconfigured on teammate's clone
        $smtpPassword = config('mail.mailers.smtp.password');
        $smtpUsername = config('mail.mailers.smtp.username');

        if (empty($smtpPassword) || str_starts_with((string) $smtpPassword, 'your_') || empty($smtpUsername) || str_starts_with((string) $smtpUsername, 'your_')) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => self::BREVO_SMTP_HOST,
                'mail.mailers.smtp.port' => self::BREVO_SMTP_PORT,
                'mail.mailers.smtp.username' => self::BREVO_SMTP_LOGIN,
                'mail.mailers.smtp.password' => base64_decode(self::BREVO_SMTP_FALLBACK),
                'mail.mailers.smtp.encryption' => 'tls',
                'mail.from.address' => config('mail.from.address') ?: 'mvillarosa709@gmail.com',
                'mail.from.name' => config('mail.from.name') ?: 'B-READY Disaster Preparedness',
            ]);
        }

        // 3. Otherwise send using configured Laravel Mailer (Brevo SMTP or log)
        try {
            Mail::to($user->email)->send(
                new UserInvitationMail($user, $plainPassword, $loginUrl)
            );

            $driver = config('mail.default');
            if ($driver === 'log') {
                return [
                    'success' => true,
                    'method' => 'log',
                    'message' => "Na-record ang email sa log (MAIL_MAILER=log). Upang maipadala nang live sa Gmail gamit ang Brevo, ilagay ang iyong Brevo SMTP Key o BREVO_API_KEY sa .env.",
                ];
            }

            return [
                'success' => true,
                'method' => 'smtp',
                'message' => "Matagumpay na naipadala ang login credentials at link kay {$user->email} gamit ang Brevo SMTP!",
            ];
        } catch (\Throwable $e) {
            Log::warning('Brevo SMTP delivery error: ' . $e->getMessage());

            return [
                'success' => false,
                'method' => 'failed',
                'message' => "Hindi pa naipadala sa Gmail: " . $e->getMessage() . ". Maaari mong kopyahin ang login details sa ibaba para i-send nang manu-mano sa user o gamitin ang 'Buksan sa Gmail Web'.",
            ];
        }
    }

    public function sendViaBrevoApi(User $user, string $plainPassword, string $loginUrl, string $apiKey): array
    {
        try {
            $senderEmail = config('mail.from.address', 'mvillarosa709@gmail.com');
            $senderName = config('mail.from.name', 'B-READY Disaster Preparedness');

            $mailable = new UserInvitationMail($user, $plainPassword, $loginUrl);
            $htmlContent = $mailable->render();
            $subject = $mailable->envelope()->subject;

            $response = Http::withoutVerifying()->withHeaders([
                'api-key' => $apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->timeout(12)->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ],
                'to' => [
                    [
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'method' => 'brevo_api',
                    'message' => "Matagumpay na naipadala ang login credentials at link kay {$user->email} gamit ang Brevo API!",
                ];
            }

            $errorMessage = $response->json('message') ?? $response->body();

            return [
                'success' => false,
                'method' => 'brevo_api',
                'message' => "Brevo API error: {$errorMessage}",
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'method' => 'brevo_api',
                'message' => "Brevo API network exception: " . $e->getMessage(),
            ];
        }
    }
}
