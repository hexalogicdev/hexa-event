<?php
namespace App\Notifications;
use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class OrganizationInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public Organization $organization, public string $acceptUrl) {}
    public function via(object $notifiable): array { return ['mail']; }
    public function toMail(object $notifiable): MailMessage { return (new MailMessage)->subject('Activate your organization account')->greeting('You have been invited to '.$this->organization->name)->line('A platform administrator created your organization account. This invitation expires in 72 hours and can be used once.')->action('Activate account',$this->acceptUrl)->line('If you were not expecting this invitation, you can ignore this email.'); }
}
