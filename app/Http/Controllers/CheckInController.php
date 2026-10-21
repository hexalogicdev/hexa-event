<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\CheckInAttempt;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckInController extends Controller
{
    public function scan()
    {
        $user = Auth::user();
        $events = Event::query()
            ->when($user->isSecurityOfficer(), fn ($query) => $query->whereHas('securityOfficers', fn ($staff) => $staff->whereKey($user->id)))
            ->when($user->isOrganizer(), fn ($query) => $query->where('user_id', $user->id))
            ->where('is_active', true)
            ->orderBy('event_start_date')
            ->get(['id', 'name', 'event_start_date', 'location']);
        $recentAttempts = CheckInAttempt::with(['event:id,name', 'attendee:id,name'])
            ->where('user_id', $user->id)->latest('attempted_at')->limit(10)->get();

        return view('checkin.scan', compact('events', 'recentAttempts'));
    }

    public function show($qr_id)
    {
        $resolvedQrId = $this->normalizeQrId($qr_id);
        $attendee = Attendee::with('event')->where('qr_id', $resolvedQrId)->first();

        if (! $attendee) {
            return redirect()->route('attendee.qr.scan')->with('error', 'Booking reference not found.');
        }

        $this->authorizeAttendee($attendee);

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
        ]);

        $resolvedQrId = $this->normalizeQrId($qr_id ?: $validated['qr_id']);

        $candidate = Attendee::with(['event', 'admissionTicket'])->where('qr_id', $resolvedQrId)->first();
        if (! $candidate) {
            $this->recordAttempt(null, $resolvedQrId, 'invalid', 'Booking reference not found.');
            return back()->withErrors(['qr_id' => 'Booking reference not found.'])->withInput();
        }

        if (! $this->canAccessAttendee($candidate)) {
            $this->recordAttempt($candidate, $resolvedQrId, 'wrong_event', 'Staff member is not assigned to this event.');
            return back()->withErrors(['qr_id' => 'This ticket belongs to an event you are not assigned to.'])->withInput();
        }

        $attendee = DB::transaction(function () use ($resolvedQrId) {
            $attendee = Attendee::with(['event', 'admissionTicket'])->where('qr_id', $resolvedQrId)->lockForUpdate()->first();

            if (! $attendee) {
                return null;
            }

            if ($attendee->checked_in) {
                return false;
            }

            if ($attendee->status !== 'confirmed' || ($attendee->admissionTicket && $attendee->admissionTicket->status !== 'valid')) {
                return 'ineligible';
            }

            $attendee->update([
                'checked_in' => true,
                'checked_in_at' => now(),
                'checked_in_by' => Auth::id(),
            ]);

            return $attendee;
        });

        if ($attendee === null) {
            $this->recordAttempt(null, $resolvedQrId, 'invalid', 'Booking reference disappeared during verification.');
            return back()->withErrors(['qr_id' => 'Booking reference not found.'])->withInput();
        }

        if ($attendee === false) {
            $this->recordAttempt($candidate, $resolvedQrId, 'duplicate', 'Attendee was already checked in.');
            return back()->withErrors(['already_checked_in' => 'Attendee has already been checked in.'])->withInput();
        }

        if ($attendee === 'ineligible') {
            $this->recordAttempt($candidate, $resolvedQrId, 'ineligible', 'Registration is not confirmed.');
            return back()->withErrors(['qr_id' => 'This registration is cancelled, pending, or otherwise ineligible.'])->withInput();
        }

        $this->recordAttempt($attendee, $resolvedQrId, 'accepted', 'Attendance recorded.');

        return view('checkin.success', compact('attendee'));
    }

    protected function authorizeAttendee(Attendee $attendee): void
    {
        abort_unless($this->canAccessAttendee($attendee), 403);
    }

    protected function canAccessAttendee(Attendee $attendee): bool
    {
        $user = Auth::user();
        if ($user->isSuperadmin()) {
            return true;
        }
        if ($user->isOrganizer()) {
            return $attendee->event->user_id === $user->id;
        }

        return $user->isSecurityOfficer()
            && $attendee->event->securityOfficers()->whereKey($user->id)->exists();
    }

    protected function recordAttempt(?Attendee $attendee, ?string $reference, string $outcome, string $message): void
    {
        CheckInAttempt::create([
            'event_id' => $attendee?->event_id,
            'attendee_id' => $attendee?->id,
            'user_id' => Auth::id(),
            'reference' => $reference,
            'outcome' => $outcome,
            'message' => $message,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 500, ''),
            'attempted_at' => now(),
        ]);
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
        $attendee = Attendee::with(['event', 'order', 'orderItem', 'admissionTicket'])->findOrFail($id);
        return view('events.thankYou', compact('attendee'));
    }
}
