<?php
namespace App\Policies;
use App\Models\Event;
use App\Models\User;
class EventPolicy
{
    public function before(User $user): ?bool { return $user->isSuperadmin() ? true : null; }
    public function view(User $user, Event $event): bool { return $event->organization_id && $event->organization ? $user->hasOrganizationRole($event->organization, ['owner','administrator','event_manager','finance_manager','marketing','checkin_manager']) : $event->user_id === $user->id; }
    public function update(User $user, Event $event): bool { return $event->organization_id && $event->organization ? $user->hasOrganizationRole($event->organization, ['owner','administrator','event_manager']) : $event->user_id === $user->id; }
    public function delete(User $user, Event $event): bool { return $this->update($user, $event); }
}
