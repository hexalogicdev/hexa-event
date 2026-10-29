<?php
namespace App\Http\Requests\Organizations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
class AcceptOrganizationInvitationRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>['required','string','max:100'],'password'=>['required','confirmed',Password::min(8)->mixedCase()->numbers()]]; }
}
