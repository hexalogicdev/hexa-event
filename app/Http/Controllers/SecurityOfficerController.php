<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Event;

class SecurityOfficerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isOrganizer() || $user->isSuperadmin(), 403);

        $officers = User::with('organizer')
            ->where('user_type', 'security_officer')
            ->when(! $user->isSuperadmin(), fn ($query) => $query->where('organizer_id', $user->id))
            ->latest()->get();
        $organizers = $user->isSuperadmin()
            ? User::where('user_type', 'organizer')->where('status', true)->orderBy('name')->get()
            : collect([$user]);

        return view('users.security-officers', compact('officers', 'organizers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isOrganizer() || $user->isSuperadmin(), 403);
        $organizerId = $user->isSuperadmin() ? $request->input('organizer_id') : $user->id;

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'organizer_id' => $user->isSuperadmin() ? ['required', Rule::exists('users', 'id')->where('user_type', 'organizer')] : 'nullable',
        ]);
        $role = Role::where('name', 'security_officer')->firstOrFail();
        User::create([
            'name' => $validated['name'], 'email' => $validated['email'],
            'password' => Hash::make($validated['password']), 'role_id' => $role->id,
            'user_type' => 'security_officer', 'organizer_id' => $organizerId, 'status' => true,
        ]);

        return back()->with('success', 'Security officer created successfully.');
    }

    public function destroy(User $securityOfficer)
    {
        $user = Auth::user();
        abort_unless($securityOfficer->isSecurityOfficer() && ($user->isSuperadmin() || $securityOfficer->organizer_id === $user->id), 403);
        $securityOfficer->delete();
        return back()->with('success', 'Security officer removed.');
    }

    public function updateStatus(Request $request, User $securityOfficer)
    {
        $user = Auth::user();
        abort_unless(
            $securityOfficer->isSecurityOfficer()
            && ($user->isSuperadmin() || $securityOfficer->organizer_id === $user->id),
            403
        );

        $validated = $request->validate(['status' => ['required', 'boolean']]);
        $securityOfficer->update(['status' => (bool) $validated['status']]);

        return back()->with('success', $securityOfficer->status
            ? 'Security officer activated.'
            : 'Security officer suspended.');
    }

    public function assignments(Event $event)
    {
        $this->authorizeEventOwner($event);
        $officers = User::query()
            ->where('user_type', 'security_officer')
            ->where('organizer_id', $event->user_id)
            ->where('status', true)
            ->orderBy('name')
            ->get();
        $assignedIds = $event->securityOfficers()->pluck('users.id')->all();

        return view('events.security-officers', compact('event', 'officers', 'assignedIds'));
    }

    public function updateAssignments(Request $request, Event $event)
    {
        $this->authorizeEventOwner($event);
        $validated = $request->validate([
            'officer_ids' => ['nullable', 'array'],
            'officer_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('user_type', 'security_officer')
                    ->where('organizer_id', $event->user_id)
                    ->where('status', true)),
            ],
        ]);

        $event->securityOfficers()->sync($validated['officer_ids'] ?? []);

        return back()->with('success', 'Event security assignments updated.');
    }

    private function authorizeEventOwner(Event $event): void
    {
        $user = Auth::user();
        abort_unless($user->isSuperadmin() || $event->user_id === $user->id, 403);
    }
}
