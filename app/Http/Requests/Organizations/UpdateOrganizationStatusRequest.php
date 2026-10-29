<?php
namespace App\Http\Requests\Organizations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateOrganizationStatusRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isSuperadmin() === true; }
    public function rules(): array { return ['status'=>['required',Rule::in(['pending','active','suspended','archived'])]]; }
}
