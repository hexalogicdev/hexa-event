<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use App\Models\Organization;
use App\Services\Organizations\EventAllowanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['registrationForm']);
        $this->middleware(function ($request, $next) {
            abort_if(Auth::user()?->isSecurityOfficer(), 403);
            return $next($request);
        })->except(['registrationForm']);
    }

    public function index()
    {
        $user = Auth::user();

        $events = Event::with('admin')->withCount([
                'attendees',
                'attendees as checked_in_total' => function ($query) {
                    $query->where('checked_in', true);
                },
            ])
            ->when(! $user?->isSuperadmin(), function ($query) use ($user) {
                $organizationIds = $user->activeOrganizations()->pluck('organizations.id');
                $query->where(function ($query) use ($user, $organizationIds) {
                    $query->whereIn('organization_id', $organizationIds)->orWhere(function ($query) use ($user) {
                        $query->whereNull('organization_id')->where('user_id', $user->id);
                    });
                });
            })
            ->latest()
            ->get()
            ->map(function ($event) {
                $event->attendee_total = $event->attendees_count ?? 0;
                $event->pending_total = max($event->attendee_total - $event->checked_in_total, 0);
                $event->is_upcoming = $event->event_start_date?->isFuture() ?? false;

                return $event;
            });

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $organizers = Auth::user()->isSuperadmin()
            ? User::where('user_type', 'organizer')->where('status', true)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        $creationToken = (string) Str::uuid();

        return view('events.create', compact('organizers', 'creationToken'));
    }

    public function store(Request $request, EventAllowanceService $allowances)
    {
        $validated = $this->validateEventRequest($request);
        $validated['event_code'] = $this->generateEventCode();
        $validated['user_id'] = Auth::user()->isSuperadmin()
            ? (int) $request->validate(['organizer_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('user_type', 'organizer')->where('status', true))]])['organizer_id']
            : Auth::id();
        $organization = User::find($validated['user_id'])?->activeOrganizations()->first();
        if (! $organization) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'organizer_id' => 'The selected organizer does not have an active organization.',
            ]);
        }
        $validated['organization_id'] = $organization->id;
        $validated['creation_token'] = $validated['creation_token'] ?? (string) Str::uuid();
        $validated['status'] = $request->input('status', 'draft');
        $validated['visibility'] = $request->input('visibility', 'public');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['price'] = $validated['ticketing_type'] === 'free' ? 0 : ($validated['price'] ?? 0);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $this->storeBannerImage($request->file('banner_image'));
        }

        $allowances->createEvent($organization, $validated, function (Event $event) use ($request) {
            $this->syncTicketTypes($event, $request);
        });

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(string $id)
    {
        $event = Event::withCount([
            'attendees',
            'attendees as checked_in_total' => function ($query) {
                $query->where('checked_in', true);
            },
        ])->findOrFail($id);
        $this->authorize('view', $event);

        $attendees = $event->attendees()->latest()->take(6)->get();
        $checkedInCount = $event->checked_in_total ?? 0;
        $pendingCount = max($event->attendees_count - $checkedInCount, 0);
        $upcomingCount = Event::query()
            ->where('user_id', $event->user_id)
            ->whereDate('event_start_date', '>=', now())
            ->count();

        return view('events.show', compact('event', 'attendees', 'checkedInCount', 'pendingCount', 'upcomingCount'));
    }

    public function edit(string $id)
    {
        $event = $this->findAccessibleEvent($id);
        $organizers = Auth::user()->isSuperadmin()
            ? User::where('user_type', 'organizer')->where('status', true)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('events.edit', compact('event', 'organizers'));
    }

    public function update(Request $request, string $id)
    {
        $event = $this->findAccessibleEvent($id);
        $validated = $this->validateEventRequest($request, $event->id);
        if (Auth::user()->isSuperadmin()) {
            $validated['user_id'] = (int) $request->validate(['organizer_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('user_type', 'organizer')->where('status', true))]])['organizer_id'];
        }
        $validated['price'] = $validated['ticketing_type'] === 'free' ? 0 : ($validated['price'] ?? $event->price);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $this->storeBannerImage($request->file('banner_image'), $event->banner_image);
        } else {
            unset($validated['banner_image']);
        }

        $event->fill($validated);
        $event->status = $request->input('status', $event->status ?? 'draft');
        $event->visibility = $request->input('visibility', $event->visibility ?? 'public');
        $event->is_active = $request->boolean('is_active', $event->is_active);
        if (isset($validated['banner_image'])) {
            $event->banner_image = $validated['banner_image'];
        }

        DB::transaction(function () use ($event, $request) {
            $event->save();
            $this->syncTicketTypes($event, $request);
        });

        return redirect()->route('events.show', $event->id)->with('success', 'Event updated successfully.');
    }

    public function fields(string $id)
    {
        $event = $this->findAccessibleEvent($id);
        $fields = $event->registration_fields ?? [];

        return view('events.fields', compact('event', 'fields'));
    }

    public function updateFields(Request $request, string $id)
    {
        $event = $this->findAccessibleEvent($id);

        $fieldRows = collect($request->input('fields', []))
            ->filter(fn ($field) => !empty($field['label']) || !empty($field['key']))
            ->values()
            ->map(function ($field, $index) {
                $label = trim((string) ($field['label'] ?? ''));
                $key = trim((string) ($field['key'] ?? ''));

                return [
                    'id' => $field['id'] ?? (string) Str::uuid(),
                    'key' => $key !== '' ? Str::slug($key) : Str::slug($label ?: 'field-' . ($index + 1)),
                    'label' => $label ?: 'Custom field ' . ($index + 1),
                    'type' => $field['type'] ?? 'text',
                    'required' => !empty($field['required']),
                    'options' => $this->normalizeFieldOptions($field['options'] ?? ''),
                ];
            })
            ->values()
            ->all();

        $event->registration_fields = $fieldRows;
        $event->save();

        return redirect()->route('events.fields', $event->id)->with('success', 'Registration fields updated.');
    }

    public function attendees(Request $request, string $id)
    {
        $event = $this->findAccessibleEvent($id);

        $attendees = $event->attendees()->latest();

        if ($search = trim((string) $request->input('q', ''))) {
            $attendees->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('qr_id', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();

            if ($status === 'checked_in') {
                $attendees->where('checked_in', true);
            }

            if ($status === 'pending') {
                $attendees->where('checked_in', false);
            }
        }

        $attendees = $attendees->get();
        $checkedInCount = $attendees->where('checked_in', true)->count();
        $pendingCount = $attendees->where('checked_in', false)->count();

        return view('events.attendees', compact('event', 'attendees', 'checkedInCount', 'pendingCount'));
    }

    public function report(string $id)
    {
        $event = $this->findAccessibleEvent($id);
        $attendees = $event->attendees()->latest()->get();
        $checkedInCount = $attendees->where('checked_in', true)->count();
        $pendingCount = $attendees->where('checked_in', false)->count();

        return view('events.report', compact('event', 'attendees', 'checkedInCount', 'pendingCount'));
    }

    public function export(string $id): StreamedResponse
    {
        $event = $this->findAccessibleEvent($id);
        $attendees = $event->attendees()->latest()->get();

        return response()->streamDownload(function () use ($attendees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'QR ID', 'Checked In', 'Checked In At']);

            foreach ($attendees as $attendee) {
                fputcsv($handle, [
                    $attendee->name,
                    $attendee->email,
                    $attendee->phone,
                    $attendee->qr_id,
                    $attendee->checked_in ? 'Yes' : 'No',
                    optional($attendee->checked_in_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'event-' . $event->id . '-attendees.csv');
    }

    public function destroy(string $id)
    {
        $event = $this->findAccessibleEvent($id);

        if ($event->banner_image && Storage::disk('public')->exists($event->banner_image)) {
            Storage::disk('public')->delete($event->banner_image);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    public function registrationForm($event_code)
    {
        $event = Event::with(['ticketTypes' => fn ($query) => $query->where('is_active', true)->withCount('attendees')])
            ->withCount('attendees')
            ->where('event_code', $event_code)
            ->firstOrFail();

        if (! $event->isPubliclyRegistrable()) {
            abort(404);
        }

        return view('events.registration', compact('event'));
    }

    private function validateEventRequest(Request $request, ?string $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_start_date' => 'required|date|after_or_equal:today',
            'event_end_date' => 'required|date|after:event_start_date',
            'booking_start_date' => 'required|date|before_or_equal:event_start_date',
            'booking_end_date' => 'required|date|after:booking_start_date|before_or_equal:event_end_date',
            'max_attendees' => 'required|integer|min:1',
            'checkin_password' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'ticketing_type' => 'required|in:free,ticketed',
            'ticket_types' => 'nullable|required_if:ticketing_type,ticketed|array|min:1',
            'ticket_types.*.id' => 'nullable|integer',
            'ticket_types.*.name' => 'required_if:ticketing_type,ticketed|nullable|string|max:100',
            'ticket_types.*.description' => 'nullable|string|max:1000',
            'ticket_types.*.price' => 'required_if:ticketing_type,ticketed|nullable|numeric|min:0|max:99999999',
            'ticket_types.*.quantity' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published',
            'visibility' => 'nullable|in:public,private',
            'banner_image' => 'nullable|image|max:4096',
            'creation_token' => 'nullable|uuid',
        ]);
    }

    private function syncTicketTypes(Event $event, Request $request): void
    {
        if ($event->ticketing_type !== 'ticketed') {
            $event->ticketTypes()->delete();
            return;
        }

        $keptIds = [];
        foreach ($request->input('ticket_types', []) as $index => $ticket) {
            $values = [
                'name' => trim((string) ($ticket['name'] ?? '')),
                'description' => trim((string) ($ticket['description'] ?? '')) ?: null,
                'price' => $ticket['price'] ?? 0,
                'quantity' => filled($ticket['quantity'] ?? null) ? (int) $ticket['quantity'] : null,
                'sort_order' => $index,
                'is_active' => true,
            ];

            $ticketType = ! empty($ticket['id'])
                ? $event->ticketTypes()->whereKey($ticket['id'])->first()
                : null;

            if ($ticketType) {
                $ticketType->update($values);
            } else {
                $ticketType = $event->ticketTypes()->create($values);
            }
            $keptIds[] = $ticketType->id;
        }

        $event->ticketTypes()->whereNotIn('id', $keptIds)->delete();
        $event->price = $event->ticketTypes()->min('price') ?? 0;
        $event->saveQuietly();
    }

    private function normalizeFieldOptions($options): array
    {
        if (is_array($options)) {
            return array_values(array_filter(array_map('trim', $options)));
        }

        if (! is_string($options) || trim($options) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $options) ?: [])));
    }

    private function storeBannerImage($file, ?string $currentPath = null): string
    {
        if ($currentPath && Storage::disk('public')->exists($currentPath)) {
            Storage::disk('public')->delete($currentPath);
        }

        return $file->store('event-banners', 'public');
    }

    private function generateEventCode(): string
    {
        do {
            $eventCode = Str::upper(Str::random(10));
        } while (Event::where('event_code', $eventCode)->exists());

        return $eventCode;
    }

    private function findAccessibleEvent(string $id): Event
    {
        $event = Event::with('ticketTypes')->withCount('attendees')->findOrFail($id);
        $this->authorize('update', $event);

        return $event;
    }
}
