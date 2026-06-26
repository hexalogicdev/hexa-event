<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckInController extends Controller
{
    public function scan()
    {
        return view('checkin.scan');
    }

    public function show($qr_id)
    {
        $resolvedQrId = $this->normalizeQrId($qr_id);
        $attendee = Attendee::with('event')->where('qr_id', $resolvedQrId)->first();

        if (! $attendee) {
            return redirect()->route('attendee.qr.scan')->with('error', 'Booking reference not found.');
        }

        return view('checkin.form', compact('attendee'));
    }

    public function verifyPassword(Request $request, $qr_id)
    {
        return $this->confirmCheckIn($request, $qr_id);
    }

    public function verifyScan(Request $request)
    {
        return $this->confirmCheckIn($request);
    }

    protected function confirmCheckIn(Request $request, ?string $qr_id = null)
    {
        $validated = $request->validate([
            'qr_id' => [$qr_id ? 'nullable' : 'required', 'string'],
            'checkin_password' => 'required|string',
        ]);

        $resolvedQrId = $this->normalizeQrId($qr_id ?: $validated['qr_id']);

        $attendee = Attendee::with('event')->where('qr_id', $resolvedQrId)->first();

        if (! $attendee) {
            return back()->withErrors(['qr_id' => 'Booking reference not found.'])->withInput();
        }

        if ($attendee->checked_in) {
            return back()->withErrors(['already_checked_in' => 'Attendee has already been checked in.'])->withInput();
        }

        if ($request->checkin_password !== $attendee->event->checkin_password) {
            return back()->withErrors(['checkin_password' => 'Invalid check-in password.'])->withInput();
        }

        $attendee->checked_in = true;
        $attendee->checked_in_at = now();
        $attendee->checked_in_by = Auth::id();
        $attendee->save();

        return view('checkin.success', compact('attendee'));
    }

    protected function normalizeQrId(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = trim((string) parse_url($value, PHP_URL_PATH), '/');
            if ($path !== '') {
                return Str::upper(Str::of($path)->afterLast('/')->toString() ?: $value);
            }
        }

        return Str::upper($value);
    }

    public function thankYou($id)
    {
        $attendee = Attendee::findOrFail($id);
        return view('events.thankYou', compact('attendee'));
    }
}
