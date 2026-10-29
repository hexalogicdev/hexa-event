<?php
namespace App\Http\Requests\Organizations;
use Illuminate\Foundation\Http\FormRequest;
class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isSuperadmin() === true; }
    public function rules(): array { return ['name'=>['required','string','max:150'],'owner_name'=>['required','string','max:100'],'owner_email'=>['required','email','max:255'],'country'=>['nullable','string','size:2'],'timezone'=>['required','timezone:all']]; }
}
