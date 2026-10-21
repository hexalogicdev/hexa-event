<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
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
        $event = Event::with('ticketTypes')->findOrFail($request->input('event_id'));
        abort_unless($event->status === 'published' && $event->visibility === 'public' && $event->is_active, 404);

        if (now()->lt($event->booking_start_date) || now()->gt($event->booking_end_date?->endOfDay())) {
            return back()->withErrors(['event_id' => 'Registration is not currently open for this event.'])->withInput();
        }
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:25',
            'idempotency_key' => 'required|string|max:100',
            'ticket_type_id' => $event->ticketing_type === 'ticketed'
                ? 'required|integer|exists:ticket_types,id'
                : 'nullable',
        ]);

        if ($event->ticketing_type === 'ticketed') {
            $ticketType = $event->ticketTypes->firstWhere('id', (int) $validated['ticket_type_id']);
            if (! $ticketType || ! $ticketType->is_active) {
                return back()->withErrors(['ticket_type_id' => 'Please select an available ticket type.'])->withInput();
            }
            // if ((float) $ticketType->price > 0) {
            //     return back()->withErrors([
            //         'ticket_type_id' => 'Paid checkout is not available until the platform payment provider is configured.',
            //     ])->withInput();
            // }
        }

        if ($existingOrder = Order::with('attendees')->where('idempotency_key', $validated['idempotency_key'])->first()) {
            $existingAttendee = $existingOrder->attendees->first();
            if ($existingAttendee) {
                return redirect(URL::temporarySignedRoute('thankYou', now()->addMinutes(30), ['id' => $existingAttendee->id]));
            }
        }

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
            $qr_id = Str::upper(Str::random(16));
        } while (Attendee::where('qr_id', $qr_id)->exists());
        $validated['qr_id'] = $qr_id;
        $validated['custom_answers'] = $customAnswers;
        $validated['status'] = 'confirmed';

        $attendee = DB::transaction(function () use ($validated, $event) {
            $lockedEvent = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();
            if ($lockedEvent->attendees()->count() >= $lockedEvent->max_attendees) {
                abort(422, 'This event has reached its registration capacity.');
            }

            $ticket = null;
            if (! empty($validated['ticket_type_id'])) {
                $ticket = $lockedEvent->ticketTypes()->whereKey($validated['ticket_type_id'])->lockForUpdate()->firstOrFail();
                if ($ticket->quantity !== null && $ticket->attendees()->count() >= $ticket->quantity) {
                    abort(422, 'The selected ticket category is sold out.');
                }
                $validated['amount'] = $ticket->price;
            } else {
                $validated['amount'] = 0;
            }

            $order = Order::create([
                'event_id' => $lockedEvent->id,
                'reference' => $this->generateOrderReference(),
                'purchaser_name' => $validated['name'],
                'purchaser_email' => $validated['email'],
                'purchaser_phone' => $validated['phone'] ?? null,
                'currency' => 'GBP',
                'subtotal' => $validated['amount'],
                'total' => $validated['amount'],
                'status' => 'completed',
                'payment_status' => 'not_required',
                'idempotency_key' => $validated['idempotency_key'],
                'completed_at' => now(),
            ]);
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'ticket_type_id' => $ticket?->id,
                'ticket_name' => $ticket?->name ?? 'General admission',
                'ticket_description' => $ticket?->description,
                'unit_price' => $validated['amount'],
                'quantity' => 1,
                'line_total' => $validated['amount'],
            ]);
            $validated['order_id'] = $order->id;
            $validated['order_item_id'] = $orderItem->id;
            unset($validated['idempotency_key']);
            $attendee = Attendee::create($validated);
            Ticket::create([
                'event_id' => $lockedEvent->id,
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'attendee_id' => $attendee->id,
                'token' => $attendee->qr_id,
                'status' => 'valid',
                'issued_at' => now(),
            ]);

            return $attendee;
        });
        return redirect(URL::temporarySignedRoute('thankYou', now()->addMinutes(30), [
            'id' => $attendee->id,
        ]));
    }

    private function generateOrderReference(): string
    {
        do {
            $reference = 'ORD-' . Str::upper(Str::random(12));
        } while (Order::where('reference', $reference)->exists());

        return $reference;
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

        if (! $user->isSuperadmin() && $attendee->event->user_id !== $user->id) {
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
