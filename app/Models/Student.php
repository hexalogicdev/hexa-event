<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
  use HasFactory;
  use SoftDeletes;
  protected $fillable = [
    'name',
    'dob',
    'parent_id',
    'teacher_id',
    'course_id',
    'language',
    'teacher_preference',
    'days',
    'start_time',
    'end_time',
    'status',
  ];
  protected $casts = [
    'start_time' => 'datetime:H:i',
    'end_time' => 'datetime:H:i',
];
  public function parent()
  {
    return $this->belongsTo(User::class, 'parent_id');
  }
  public function teacher()
  {
    return $this->belongsTo(User::class, 'teacher_id');
  }
  public function classSessions()
  {
    return $this->hasMany(ClassSession::class, 'student_id');
  }
  public function course(){
    return $this->belongsTo(Course::class,'course_id');
  }
}
