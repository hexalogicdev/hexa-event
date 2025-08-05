<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $fillable = [
    'teacher_id',
    'parent_id',
    'student_id',
    'course_id',
    'date',
    'start_time',
    'end_time',
    'meeting_link',
    'teacher_remarks',
    'parent_remarks',
    'status',
  ];
  protected $casts = [
    'date' => 'date',
    'start_time' => 'datetime:H:i',
    'end_time' => 'datetime:H:i',
  ];

  // Relationship with the teacher (User)
  public function teacher()
  {
    return $this->belongsTo(User::class, 'teacher_id');
  }

  // Relationship with the student
  public function student()
  {
    return $this->belongsTo(Student::class, 'student_id');
  }

  // Relationship with the course
  public function course()
  {
    return $this->belongsTo(Course::class);
  }
}
