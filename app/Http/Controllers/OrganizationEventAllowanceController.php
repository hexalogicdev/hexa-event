<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organizations\StoreEventAllowanceAdjustmentRequest;
use App\Models\Organization;

class OrganizationEventAllowanceController extends Controller
{
    public function store(StoreEventAllowanceAdjustmentRequest $request, Organization $organization)
    {
        $organization->allowanceAdjustments()->create($request->validated() + ['adjusted_by' => $request->user()->id]);

        return back()->with('success', 'Event allowance adjustment recorded.');
    }
}
