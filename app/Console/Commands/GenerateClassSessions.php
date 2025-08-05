<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateClassSessions extends Command
{
  protected $signature = 'classes:generate';
  protected $description = 'Generate class sessions for students based on their scheduled days';

  public function handle()
  {
    $today = Carbon::now();
    $dayName = strtolower($today->format('l')); // e.g., 'monday'
    Log::channel('class_scheduler')->info("===== START: Class session generation for date: {$today->toDateString()} ({$dayName}) =====");

    $students = Student::where('status', true)->get();
    Log::channel('class_scheduler')->info("Fetched " . count($students) . " active students.");

    foreach ($students as $student) {
      Log::channel('class_scheduler')->info("Processing student ID: {$student->id}, Name: {$student->name}");

      $studentDays = json_decode($student->days, true);
      Log::channel('class_scheduler')->info("Student scheduled days: " . json_encode($studentDays));

      if (!is_array($studentDays)) {
        Log::channel('class_scheduler')->warning("Invalid or empty schedule format for student ID: {$student->id}");
        continue;
      }

      if (in_array($dayName, array_map('strtolower', $studentDays))) {
        Log::channel('class_scheduler')->info("Today ({$dayName}) is a scheduled day for student ID: {$student->id}");

        $alreadyExists = ClassSession::where([
          ['student_id', $student->id],
          ['date', $today->toDateString()],
        ])->exists();

        if ($alreadyExists) {
          Log::channel('class_scheduler')->info("Session already exists for student ID: {$student->id} on {$today->toDateString()}");
          continue;
        }

        Log::channel('class_scheduler')->info("Creating class session for student ID: {$student->id}");
        $startTime = Carbon::parse($student->start_time);
        $endTime = $startTime->copy()->addMinutes(30);
        ClassSession::create([
          'teacher_id' => $student->teacher_id,
          'parent_id' => $student->parent_id,
          'student_id' => $student->id,
          'course_id' => $student->course_id,
          'date' => $today->toDateString(),
          'start_time' => $startTime->format('H:i:s'),
          'end_time' => $endTime->format('H:i:s'),
          'meeting_link' => null,
          'status' => 'scheduled',
        ]);

        Log::channel('class_scheduler')->info("Session successfully created for student ID: {$student->id}");
      } else {
        Log::channel('class_scheduler')->info("Today ({$dayName}) is NOT a scheduled day for student ID: {$student->id}");
      }
    }

    Log::channel('class_scheduler')->info("===== END: Completed session generation for {$today->toDateString()} =====");
  }
}
