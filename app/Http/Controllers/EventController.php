<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['registrationForm']);
    }

    public function index()
    {
        $user = Auth::user();

        $events = Event::withCount([
                'attendees',
                'attendees as checked_in_total' => function ($query) {
                    $query->where('checked_in', true);
                },
            ])
            ->when($user?->role?->name !== 'admin', function ($query) use ($user) {
                $query->where('user_id', $user->id);
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
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEventRequest($request);
        $validated['event_code'] = $this->generateEventCode();
        $validated['user_id'] = Auth::id();
        $validated['status'] = $request->input('status', 'draft');
        $validated['visibility'] = $request->input('visibility', 'public');
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $this->storeBannerImage($request->file('banner_image'));
        }

        Event::create($validated);

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
        $user = Auth::user();

        if ($user?->role?->name !== 'admin' && $event->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

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

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, string $id)
    {
        $event = $this->findAccessibleEvent($id);
        $validated = $this->validateEventRequest($request, $event->id);

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

        $event->save();

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
        $event = Event::where('event_code', $event_code)->firstOrFail();

        if ($event->status === 'draft') {
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
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published',
            'visibility' => 'nullable|in:public,private',
            'banner_image' => 'nullable|image|max:4096',
        ]);
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
        $event = Event::withCount('attendees')->findOrFail($id);
        $user = Auth::user();

        if ($user?->role?->name !== 'admin' && $event->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        return $event;
    }
}
