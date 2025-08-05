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
    ];
  }
  public function role()
  {
    return $this->belongsTo(Role::class);
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
