<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
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
        $event = Event::findOrFail($request->input('event_id'));
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:25',
        ]);

        $customAnswers = [];
        foreach (($event->registration_fields ?? []) as $field) {
            $fieldKey = $field['key'] ?? null;
            if (! $fieldKey) {
                continue;
            }

            $fieldValue = $request->input($fieldKey);

            if (($field['required'] ?? false) && ($fieldValue === null || $fieldValue === '' || (is_array($fieldValue) && empty($fieldValue)))) {
                return back()->withErrors([$fieldKey => $field['label'] . ' is required.'])->withInput();
            }

            if ($request->hasFile($fieldKey)) {
                $customAnswers[$fieldKey] = $request->file($fieldKey)->store('attendee-files', 'public');
                continue;
            }

            $customAnswers[$fieldKey] = $fieldValue;
        }

        do {
            $qr_id = Str::upper(Str::random(10));
        } while (Attendee::where('qr_id', $qr_id)->exists());
        $validated['qr_id'] = $qr_id;
        $validated['custom_answers'] = $customAnswers;
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
        $attendee = Attendee::where('qr_id', Str::upper($qr_id))->firstOrFail();

        return view('events.qr-scan-result', compact('attendee'));
    }

    public function manualCheckIn(Request $request, Attendee $attendee)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if ($user->role?->name !== 'admin' && $attendee->event->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        if ($attendee->checked_in) {
            return back()->with('error', 'Already Checked In');
        }

        $attendee->checked_in = true;
        $attendee->checked_in_at = now();
        $attendee->checked_in_by = $user->id;
        $attendee->save();

        return back()->with('success', 'Attendee checked in successfully.');
    }
}
