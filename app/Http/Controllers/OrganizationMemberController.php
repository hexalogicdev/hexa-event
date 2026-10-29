<?php
namespace App\Http\Controllers;
use App\Http\Requests\Organizations\InviteOrganizationMemberRequest;
use App\Models\Organization;
use App\Services\Organizations\OrganizationInvitationService;
class OrganizationMemberController extends Controller
{
    public function store(InviteOrganizationMemberRequest $request,Organization $organization,OrganizationInvitationService $service) { $service->invite($organization,$request->validated('name'),$request->validated('email'),$request->validated('role'),$request->user()); return back()->with('success','Team invitation queued.'); }
}
