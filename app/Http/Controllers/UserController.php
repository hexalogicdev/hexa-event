<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\ClassSession;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */

  public function __construct()
  {
    $this->middleware('auth');
  }
  public function index()
  {
    if (Auth::user()->role->name == "admin") {
      $teachers = User::where('user_type', 'teacher')->get();
      return view('users.teachers', ['teachers' => $teachers]);
    } else {
      return redirect()->back()->with('error', 'No Access');
    }
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
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|min:3|max:20',
      'email' => 'required|email|max:255|unique:users,email',
      'password' => [
        'required',
        'string',
        'min:8',
        'max:64',
        'confirmed',
        'regex:/[A-Z]/',
        'regex:/[a-z]/',
        'regex:/[!@#$%^&*(),.?":{}|<>]/'
      ],
    ], [
      'name.required' => 'The name field is mandatory.',
      'name.min' => 'The name must be at least 3 characters long.',
      'name.max' => 'The name may not be greater than 20 characters.',
      'email.required' => 'Email is required.',
      'email.email' => 'Email must be a valid email address.',
      'email.unique' => 'Email has already been taken.',
      'password.required' => 'Password is required.',
      'password.min' => 'Password must be at least 8 characters.',
      'password.max' => 'Password may not be greater than 64 characters.',
      'password.confirmed' => 'Password and confirm password do not match.',
      'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter and one special character',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator, 'add_employee')
        ->withInput();
    }
    $role = Role::where('name', 'teacher')->first();
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->user_type = 'teacher';
    $user->role_id = $role->id;
    $user->save();
    return redirect()->back()->with('success', 'Teacher created');
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
    $user = User::find($id);
    if ($user) {
      if ($request->type == "change_password") {
        $validator = Validator::make(
          $request->all(),
          [
            'password' => [
              'required',
              'string',
              'min:8',
              'max:64',
              'confirmed',
              'regex:/[A-Z]/',
              'regex:/[a-z]/',
              'regex:/[!@#$%^&*(),.?":{}|<>]/'
            ],
          ],
          [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 64 characters.',
            'password.confirmed' => 'Password and confirm password do not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter and one special character',
          ]
        );

        if ($validator->fails()) {
          return redirect()->back()
            ->withErrors($validator, 'change_password')
            ->withInput()
            ->with('show_modal', $user->id);
        }
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return redirect()->back()->with('success', 'Password Updated');
      }
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3|max:20',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
      ], [
        'name.required' => 'Name is required.',
        'name.min' => 'Name must be at least 3 characters.',
        'name.max' => 'Name must not exceed 20 characters.',
        'email.required' => 'Email is required.',
        'email.email' => 'Email must be a valid email address.',
        'email.unique' => 'Email already exists.',
      ]);

      if ($validator->fails()) {
        return redirect()->back()
          ->withErrors($validator, 'editEmployee')
          ->withInput($request->only('name', 'email', 'id'))
          ->with('show_modal', $user->id);
      }
      $user->name = $request->name;
      $user->email = $request->email;
      $user->save();
      return redirect()->back()->with('success', 'Teacher updated');
    }
    return redirect()->back()->with('error', 'Teacher not found');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $user = User::find($id);

    if ($user) {
      // Generate a unique random email address
      do {
        $randomString = Str::random(8);
        $randomEmail = "{$randomString}@softdelete.com";
      } while (User::where('email', $randomEmail)->exists());

      // Update the user's email and perform soft delete
      $user->email = $randomEmail;
      $user->save();
      $user->delete();

      return redirect()->back()->with('success', 'Employee deleted');
    }

    return redirect()->back()->with('error', 'Employee not found');
  }
  public function dashboard()
  {
    $user = Auth::user();
    $isAdmin = $user?->role?->name === 'admin';
    $eventQuery = Event::withCount('attendees')->latest();

    if (! $isAdmin) {
      $eventQuery->where('user_id', $user->id);
    }

    $events = $eventQuery->get();
    $recentEvents = $events->take(3)->values();
    $upcomingEvents = $events->filter(fn ($event) => $event->event_start_date?->isFuture())->values();
    $publishedEvents = $events->where('status', 'published')->count();
    $draftEvents = $events->where('status', 'draft')->count();
    $eventsCount = $events->count();
    $attendeeQuery = Attendee::with('event')->latest();

    if (! $isAdmin) {
      $attendeeQuery->whereHas('event', function ($query) use ($user) {
        $query->where('user_id', $user->id);
      });
    }

    $attendees = (clone $attendeeQuery)->count();
    $revenue = (clone $attendeeQuery)->sum('amount');
    $checkedInCount = (clone $attendeeQuery)->where('checked_in', true)->count();
    $pendingCount = max($attendees - $checkedInCount, 0);
    $recentAttendees = $attendeeQuery->take(5)->get();
    $attendanceRate = $attendees > 0 ? round(($checkedInCount / $attendees) * 100) : 0;

    return view('dashboards.admin_dashboard', compact(
      'eventsCount',
      'events',
      'recentEvents',
      'upcomingEvents',
      'publishedEvents',
      'draftEvents',
      'attendees',
      'revenue',
      'recentAttendees',
      'checkedInCount',
      'pendingCount',
      'attendanceRate'
    ));
  }

  public function profile()
  {
    return view('profile.show', [
      'user' => Auth::user(),
    ]);
  }

  public function updateProfile(Request $request)
  {
    $user = Auth::user();

    if (! $user) {
      abort(403);
    }

    $type = $request->input('type', 'profile');

    if ($type === 'change_password') {
      $validator = Validator::make(
        $request->all(),
        [
          'password' => [
            'required',
            'string',
            'min:8',
            'max:64',
            'confirmed',
            'regex:/[A-Z]/',
            'regex:/[a-z]/',
            'regex:/[!@#$%^&*(),.?":{}|<>]/'
          ],
        ],
        [
          'password.required' => 'Password is required.',
          'password.min' => 'Password must be at least 8 characters.',
          'password.max' => 'Password must not exceed 64 characters.',
          'password.confirmed' => 'Password and confirm password do not match.',
          'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter and one special character',
        ]
      );

      if ($validator->fails()) {
        return redirect()->back()
          ->withErrors($validator, 'change_password')
          ->withInput()
          ->with('show_modal', $user->id);
      }

      $user->password = bcrypt($request->input('password'));
      $user->save();

      return redirect()->route('profile.show')->with('success', 'Password updated successfully.');
    }

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|min:3|max:20',
      'email' => 'required|email|max:255|unique:users,email,' . $user->id,
    ], [
      'name.required' => 'Name is required.',
      'name.min' => 'Name must be at least 3 characters.',
      'name.max' => 'Name must not exceed 20 characters.',
      'email.required' => 'Email is required.',
      'email.email' => 'Email must be a valid email address.',
      'email.unique' => 'Email already exists.',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator, 'profile')
        ->withInput();
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
  }

  public function changeStatus($model, $id)
  {
    $modelClass = 'App\\Models\\' . $model;
    $record = $modelClass::find($id);
    if ($record) {
      $record->status = $record->status == 1 ? 0 : 1;
      $record->save();
      return redirect()->back()->with('success', 'Status Updated successfully');
    }
    return redirect()->back()->with('error', 'Record not found');
  }
}
