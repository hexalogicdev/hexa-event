<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
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
    
    $courses = Course::all();
    return view('courses.index', ['courses' => $courses]);
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
    // 1. Validate
    $validated = $request->validate([
      'title' => 'required|string|max:255',
    ]);
    $course = new Course($validated);
    $course->save();

    return back()->with('success', 'Course added successfully');
  }


  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
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
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $course = Course::findOrFail($id);
    $course->delete();
    return back()->with('success', 'Course deleted successfully.');
  }
}
