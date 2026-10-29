<?php

namespace App\Notifications;

use App\Models\EventAllowanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventAllowanceRequestReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventAllowanceRequest $allowanceRequest) {}
    public function via(object $notifiable): array { return ['mail']; }
    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->allowanceRequest;
        return (new MailMessage)
            ->subject('Event allowance request '.str_replace('_', ' ', $request->status))
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your request for '.$request->requested_quantity.' additional event slots is now '.str_replace('_', ' ', $request->status).'.')
            ->when($request->status === 'approved', fn (MailMessage $mail) => $mail->line('Approved quantity: '.$request->approved_quantity.'.'))
            ->action('View organization', route('organizations.show', $request->organization_id));
    }
}
