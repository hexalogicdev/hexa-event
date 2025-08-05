<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;

class StudentController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function __construct()
  {
    return $this->middleware('auth');
  }
  public function index()
  {
    $user = Auth::user();

    // Admin View: See all students, teachers, and courses
    if ($user?->role?->name === 'admin') {
      $teachers = User::where('user_type', 'teacher')->get();
      $courses = Course::all();
      $students = Student::all();

      return view('users.students', compact('students', 'teachers', 'courses'));
    }

    // Parent View: See only their own children
    if ($user?->role?->name === 'parent') {
      $students = Student::where('parent_id', $user->id)->get();
      return view('users.childrens', compact('students'));
    }

    // Teacher View: See only assigned students
    if ($user?->role?->name === 'teacher') {
      $students = Student::where('teacher_id', $user->id)->get();
      return view('users.childrens', compact('students'));
    }

    // Optional fallback for unknown role or guest
    abort(403, 'Unauthorized access');
  }


  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'languages' => 'required|array',
      'teacher_prefernece' => 'required|string',
      'days' => 'required|array',
      'start_time' => 'required',
    ]);

    $student = new Student();
    $student->parent_id = Auth::user()->id;
    $student->name = $validated['name'];
    $student->language = json_encode($validated['languages']); // store array as JSON
    $student->teacher_preference = $validated['teacher_prefernece'];
    $student->days = json_encode($validated['days']); // store array as JSON
    $student->start_time = $validated['start_time'];
    $student->save();

    return back()->with('success', 'Student registered successfully.');
  }


  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $student = Student::find($id);
    $classes = ClassSession::where('student_id', $id)->get();
    return view('class_sessions.parent_childs_detail', ['classes' => $classes, 'student' => $student]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    // Validate request
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'teacher' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    // Find and update student
    $student = Student::findOrFail($id);

    $student->name = $request->name;
    $student->teacher_id = $request->teacher;
    $student->course_id = $request->course;
    $student->save();
    return redirect()->back()->with('success', 'Student updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $student = Student::findOrFail($id);
    $student->delete();
    return back()->with('success', 'student deleted successfully.');
  }
}
