<?php

namespace App\Jobs;

use App\Mail\IncidentNotificationMail;
use App\Models\Incident;
use App\Models\NotificationDelivery;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendIncidentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $incidentId, public int $userId, public string $event)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $incident = Incident::with('monitor')->findOrFail($this->incidentId);
        $user = User::findOrFail($this->userId);

        if (! $user->alert_notifications_enabled) {
            return;
        }

        $delivery = NotificationDelivery::firstOrCreate([
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'event' => $this->event,
        ]);

        if ($delivery->status === 'sent') {
            return;
        }

        try {
            Mail::to($user)->send(new IncidentNotificationMail($incident, $this->event));
            $delivery->update(['status' => 'sent', 'sent_at' => now(), 'error_message' => null]);
        } catch (Throwable $exception) {
            $delivery->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
