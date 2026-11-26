<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $plainPassword,
        public ?string $loginUrl = null
    ) {
        $this->loginUrl = $loginUrl ?? route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $roleName = match ($this->user->role) {
            'trainer' => 'DRR Trainer',
            'admin' => 'Super Administrator',
            default => 'Teacher',
        };

        return new Envelope(
            subject: "Your B-READY Platform Account ({$roleName}) — Login Credentials",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
