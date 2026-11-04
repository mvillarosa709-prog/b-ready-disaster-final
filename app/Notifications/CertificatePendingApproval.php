<?php

namespace App\Notifications;

use App\Models\Certification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CertificatePendingApproval extends Notification
{
    use Queueable;

    public function __construct(public Certification $certification) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certificate_id' => $this->certification->id,
            'certificate_number' => $this->certification->certificate_number,
            'recipient_name' => $this->certification->teacher->name,
            'workshop_title' => $this->certification->workshop->title,
            'message' => 'A new certificate is waiting for approval and signature.',
            'url' => route('trainer.certificates.pending'),
        ];
    }
}
