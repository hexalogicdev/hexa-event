<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendee;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['registrationForm']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_start_date' => 'required|date|after_or_equal:today',
            'event_end_date' => 'required|date|after:event_start_date',
            'booking_start_date' => 'required|date|before_or_equal:event_start_date',
            'booking_end_date' => 'required|date|after:booking_start_date|before_or_equal:event_end_date',
            'max_attendees' => 'required|integer|min:1',
            'checkin_password' => 'required|string|max:5',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        do {
            $event_code = Str::random(10);
        } while (Event::where('event_code', $event_code)->exists());
        $request->merge(['event_code' => $event_code]);
        $request->merge(['user_id' => Auth::user()->id]);

        Event::create($request->all());

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendees = Attendee::where('event_id', $id)->get();
        return view('events.attendees', compact('attendees'));
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
    public function destroy(string $id)
    {
        //
    }
    public function registrationForm($event_code)
    {
        $event = Event::where('event_code', $event_code)->firstOrFail();
        return view('events.registration', compact('event'));
    }
}
