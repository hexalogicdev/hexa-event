<?php
namespace App\Http\Controllers;
use App\Http\Requests\Organizations\AcceptOrganizationInvitationRequest;
use App\Services\Organizations\OrganizationInvitationService;
use Illuminate\Support\Facades\Auth;
class OrganizationInvitationController extends Controller
{
    public function show(string $token,OrganizationInvitationService $service) { return view('invitations.organization',['invitation'=>$service->findUsable($token),'token'=>$token]); }
    public function accept(AcceptOrganizationInvitationRequest $request,string $token,OrganizationInvitationService $service) { $user=$service->accept($service->findUsable($token),$request->validated('name'),$request->validated('password')); Auth::login($user); $request->session()->regenerate(); return redirect()->route('dashboard')->with('success','Your organization account is active.'); }
}
