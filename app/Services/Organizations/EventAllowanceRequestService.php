<?php

namespace App\Services\Organizations;

use App\Models\EventAllowanceRequest;
use App\Models\User;
use App\Notifications\EventAllowanceRequestReviewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventAllowanceRequestService
{
    public function review(EventAllowanceRequest $allowanceRequest, User $administrator, string $action, ?int $approvedQuantity, ?string $notes): EventAllowanceRequest
    {
        $reviewed = DB::transaction(function () use ($allowanceRequest, $administrator, $action, $approvedQuantity, $notes) {
            $request = EventAllowanceRequest::query()->lockForUpdate()->findOrFail($allowanceRequest->id);
            if (! in_array($request->status, ['pending', 'under_review'], true)) {
                throw ValidationException::withMessages(['action' => 'This request has already been finalized.']);
            }

            if ($action === 'under_review') {
                $request->update(['status' => 'under_review', 'reviewed_by' => $administrator->id, 'internal_notes' => $notes]);
                return $request;
            }

            $status = $action === 'approve' ? 'approved' : 'rejected';
            $request->update(['status' => $status, 'reviewed_by' => $administrator->id, 'approved_quantity' => $status === 'approved' ? $approvedQuantity : null, 'internal_notes' => $notes, 'reviewed_at' => now()]);

            if ($status === 'approved') {
                $request->organization->allowanceAdjustments()->create([
                    'adjusted_by' => $administrator->id,
                    'quantity' => $approvedQuantity,
                    'reason' => 'Approved allowance request #'.$request->id,
                    'notes' => $notes,
                    'agreement_reference' => 'ALLOWANCE-REQUEST-'.$request->id,
                    'effective_date' => now()->toDateString(),
                ]);
            }

            return $request;
        }, 3);

        $reviewed->load(['organization', 'requester']);
        $reviewed->requester?->notify(new EventAllowanceRequestReviewedNotification($reviewed));
        return $reviewed;
    }

    public function cancel(EventAllowanceRequest $allowanceRequest, User $user): EventAllowanceRequest
    {
        return DB::transaction(function () use ($allowanceRequest, $user) {
            $request = EventAllowanceRequest::query()->lockForUpdate()->findOrFail($allowanceRequest->id);
            abort_unless($request->requested_by === $user->id || $user->isSuperadmin(), 403);
            if (! in_array($request->status, ['pending', 'under_review'], true)) {
                throw ValidationException::withMessages(['request' => 'Only open requests can be cancelled.']);
            }
            $request->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            return $request;
        }, 3);
    }
}
