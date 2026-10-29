<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Role;

class User extends Authenticatable
{
  use HasFactory, Notifiable, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'phone',
    'password',
    'user_type',
    'country',
    'role_id',
    'organizer_id',
    'gender',
    'dob',
    'address',
    'timezone',
    'photo',
    'qualification',
    'experience',
    'level',
    'bio',
    'status',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'status' => 'boolean',
    ];
  }
  public function role()
  {
    return $this->belongsTo(Role::class);
  }
  public function events()
  {
    return $this->hasMany(Event::class);
  }
  public function isSuperadmin(): bool
  {
    return $this->role?->name === 'superadmin' || $this->user_type === 'superadmin';
  }

  public function isOrganizer(): bool
  {
    return $this->role?->name === 'organizer' || $this->user_type === 'organizer';
  }
  public function isSecurityOfficer(): bool
  {
    return $this->role?->name === 'security_officer' || $this->user_type === 'security_officer';
  }
  public function organizer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'organizer_id');
  }
  public function securityOfficers()
  {
    return $this->hasMany(User::class, 'organizer_id')->where('user_type', 'security_officer');
  }
  public function assignedEvents()
  {
    return $this->belongsToMany(Event::class, 'event_security_officer')->withTimestamps();
  }
  public function organizations()
  {
    return $this->belongsToMany(Organization::class, 'organization_memberships')
      ->withPivot(['role', 'status', 'invited_by', 'joined_at'])->withTimestamps();
  }
  public function ownedOrganizations()
  {
    return $this->hasMany(Organization::class, 'owner_id');
  }
  public function activeOrganizations()
  {
    return $this->organizations()->wherePivot('status', 'active')->where('organizations.status', 'active');
  }
  public function hasOrganizationRole(Organization $organization, array|string $roles): bool
  {
    if ($this->isSuperadmin()) {
      return true;
    }

    return $this->organizations()->whereKey($organization->id)
      ->where('organizations.status', 'active')
      ->wherePivot('status', 'active')->wherePivotIn('role', (array) $roles)->exists();
  }
  public function checkInAttempts()
  {
    return $this->hasMany(CheckInAttempt::class);
  }
  // If user is a parent
  public function children()
  {
    return $this->hasMany(Student::class, 'parent_id');
  }

  // If user is a teacher
  public function students()
  {
    return $this->hasMany(Student::class, 'teacher_id');
  }
  public function classSessionsAsTeacher()
{
    return $this->hasMany(ClassSession::class, 'teacher_id');
}
}
