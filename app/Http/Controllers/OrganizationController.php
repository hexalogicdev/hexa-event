<?php
namespace App\Http\Controllers;
use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Models\Organization;
use App\Services\Organizations\OrganizationInvitationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Organizations\EventAllowanceService;
class OrganizationController extends Controller
{
    public function index() { $this->authorize('viewAny',Organization::class); $query=Organization::with('owner')->withCount(['memberships','events'])->latest(); if (! auth()->user()->isSuperadmin()) $query->whereHas('memberships',fn ($membership)=>$membership->where('user_id',auth()->id())->where('status','active')); $organizations=$query->paginate(25); return view('organizations.index',compact('organizations')); }
    public function show(Organization $organization, EventAllowanceService $allowances) { $this->authorize('view',$organization); $organization->load(['owner','memberships.user','invitations'=>fn($query)=>$query->latest(),'allowanceAdjustments.administrator','allowanceRequests'=>fn($query)=>$query->with(['requester','reviewer'])->latest()])->loadCount('events'); $allowance=$allowances->summary($organization); return view('organizations.show',compact('organization','allowance')); }
    public function store(StoreOrganizationRequest $request,OrganizationInvitationService $invitations) { $organization=DB::transaction(function () use ($request) { $data=$request->validated(); return Organization::create(['name'=>$data['name'],'slug'=>Str::slug($data['name']).'-'.Str::lower(Str::random(6)),'status'=>'pending','email'=>Str::lower($data['owner_email']),'country'=>$data['country']??null,'timezone'=>$data['timezone']]); }); $invitations->inviteOwner($organization,$request->validated('owner_name'),$request->validated('owner_email'),$request->user()); return redirect()->route('organizations.index')->with('success','Organization created and its owner invitation queued.'); }
    public function updateStatus(\App\Http\Requests\Organizations\UpdateOrganizationStatusRequest $request,Organization $organization) { $status=$request->validated('status'); $organization->update(['status'=>$status,'activated_at'=>$status==='active'?($organization->activated_at??now()):$organization->activated_at,'suspended_at'=>$status==='suspended'?now():null,'archived_at'=>$status==='archived'?now():null]); return back()->with('success','Organization status updated.'); }
}
