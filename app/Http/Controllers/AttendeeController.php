<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use Illuminate\Support\Str;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:25',
        ]);
        do {
            $qr_id  = Str::random(10);
        } while (Attendee::where('qr_id', $qr_id)->exists());
        $validated['qr_id'] = $qr_id;
        $attendee = Attendee::create($validated);
        return redirect()->route('thankYou', $attendee->id);
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
    public function destroy(string $id)
    {
        //
    }
    
    public function handleQrScan($qr_id)
    {
        $attendee = Attendee::where('qr_id', $qr_id)->firstOrFail();

        return view('events.qr-scan-result', compact('attendee'));
    }
}
