<?php
namespace App\Services\Organizations;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class OrganizationInvitationService
{
    public function inviteOwner(Organization $organization,string $name,string $email,User $inviter): OrganizationInvitation
    {
        return $this->invite($organization,$name,$email,'owner',$inviter);
    }
    public function invite(Organization $organization,string $name,string $email,string $role,User $inviter): OrganizationInvitation
    {
        return DB::transaction(function () use ($organization,$name,$email,$role,$inviter) { $organization->invitations()->where('email',Str::lower($email))->whereNull('accepted_at')->whereNull('revoked_at')->update(['revoked_at'=>now()]); $token=Str::random(64); $invitation=$organization->invitations()->create(['name'=>$name,'email'=>Str::lower($email),'role'=>$role,'token_hash'=>hash('sha256',$token),'invited_by'=>$inviter->id,'expires_at'=>now()->addHours(72)]); Notification::route('mail',$email)->notify(new OrganizationInvitationNotification($organization,route('organization-invitations.show',['token'=>$token]))); return $invitation; });
    }
    public function findUsable(string $token): OrganizationInvitation { $invitation=OrganizationInvitation::with('organization')->where('token_hash',hash('sha256',$token))->firstOrFail(); abort_unless($invitation->isUsable(),410,'This invitation is expired, revoked, or already used.'); return $invitation; }
    public function accept(OrganizationInvitation $invitation,string $name,string $password): User
    {
        if (! $invitation->isUsable()) throw ValidationException::withMessages(['invitation'=>'This invitation is no longer available.']);
        return DB::transaction(function () use ($invitation,$name,$password) { $locked=OrganizationInvitation::whereKey($invitation->id)->lockForUpdate()->firstOrFail(); if (! $locked->isUsable()) throw ValidationException::withMessages(['invitation'=>'This invitation is no longer available.']); $user=User::withTrashed()->where('email',$locked->email)->first(); if (! $user) { $globalRole=$locked->role === 'security_officer' ? 'security_officer' : 'organizer'; $role=Role::where('name',$globalRole)->firstOrFail(); $user=new User(['email'=>$locked->email,'user_type'=>$globalRole,'role_id'=>$role->id,'name'=>$name,'password'=>Hash::make($password),'status'=>true]); $user->save(); } elseif ($user->trashed()) { $user->restore(); } OrganizationMembership::updateOrCreate(['organization_id'=>$locked->organization_id,'user_id'=>$user->id],['role'=>$locked->role,'status'=>'active','invited_by'=>$locked->invited_by,'joined_at'=>now()]); if ($locked->role === 'owner') $locked->organization()->update(['owner_id'=>$user->id,'status'=>'active','email'=>$user->email,'activated_at'=>now(),'suspended_at'=>null]); $locked->update(['accepted_at'=>now()]); return $user; });
    }
}
