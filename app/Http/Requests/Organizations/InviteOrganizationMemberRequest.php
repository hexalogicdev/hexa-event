<?php
namespace App\Http\Requests\Organizations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class InviteOrganizationMemberRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('manageMembers',$this->route('organization')) === true; }
    public function rules(): array { return ['name'=>['required','string','max:100'],'email'=>['required','email','max:255'],'role'=>['required',Rule::in(['administrator','event_manager','finance_manager','marketing','checkin_manager','security_officer'])]]; }
}
