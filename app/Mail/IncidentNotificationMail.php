<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Incident $incident, public string $event) {}

    public function envelope(): Envelope
    {
        $state = $this->event === 'opened' ? 'Incident opened' : 'Service recovered';

        return new Envelope(subject: "[PulseMonitor] {$state}: {$this->incident->monitor->name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.incident-notification', with: ['incident' => $this->incident, 'event' => $this->event]);
    }
}
