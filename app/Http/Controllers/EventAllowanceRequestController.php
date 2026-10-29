<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organizations\ReviewEventAllowanceRequest;
use App\Http\Requests\Organizations\StoreEventAllowanceRequest;
use App\Models\EventAllowanceRequest;
use App\Models\Organization;
use App\Services\Organizations\EventAllowanceRequestService;
use App\Services\Organizations\EventAllowanceService;
use Illuminate\Http\Request;

class EventAllowanceRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isSuperadmin(), 403);
        $requests = EventAllowanceRequest::with(['organization', 'requester', 'reviewer'])->latest()->paginate(25);
        $organizations = Organization::withCount('events')->withSum('allowanceAdjustments as allocated_allowance', 'quantity')->orderBy('name')->get();
        $openRequestCount = EventAllowanceRequest::whereIn('status', ['pending', 'under_review'])->count();
        return view('allowance-requests.index', compact('requests', 'organizations', 'openRequestCount'));
    }

    public function store(StoreEventAllowanceRequest $request, Organization $organization)
    {
        $organization->allowanceRequests()->create($request->validated() + ['requested_by' => $request->user()->id, 'status' => 'pending']);
        return back()->with('success', 'Additional event allowance requested.');
    }

    public function review(ReviewEventAllowanceRequest $request, EventAllowanceRequest $allowanceRequest, EventAllowanceRequestService $service)
    {
        $data = $request->validated();
        $service->review($allowanceRequest, $request->user(), $data['action'], $data['approved_quantity'] ?? null, $data['internal_notes'] ?? null);
        return back()->with('success', 'Allowance request updated.');
    }

    public function cancel(Request $request, EventAllowanceRequest $allowanceRequest, EventAllowanceRequestService $service)
    {
        $service->cancel($allowanceRequest, $request->user());
        return back()->with('success', 'Allowance request cancelled.');
    }
}
