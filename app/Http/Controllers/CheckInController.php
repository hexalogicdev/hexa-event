<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;

class CheckInController extends Controller
{
    public function show($qr_id)
    {
        $attendee = Attendee::with('event')->where('qr_id', $qr_id)->firstOrFail();
        return view('checkin.form', compact('attendee'));
    }

    public function verifyPassword(Request $request, $qr_id)
    {
        $attendee = Attendee::with('event')->where('qr_id', $qr_id)->firstOrFail();
        if ($attendee->checked_in) {
            return back()->withErrors(['already_checked_in' => 'Attendee has already been checked in.']);
        }

        $request->validate([
            'checkin_password' => 'required|string',
        ]);

        if ($request->checkin_password === $attendee->event->checkin_password) {
            $attendee->checked_in = true;
            $attendee->checked_in_at = now(); // optional timestamp
            $attendee->save();

            return view('checkin.success', compact('attendee'));
        } else {
            return back()->withErrors(['checkin_password' => 'Invalid check-in password.']);
        }
    }
    public function thankYou($id)
    {
        $attendee = Attendee::findOrFail($id);
        return view('events.thankYou', compact('attendee'));
    }
}
