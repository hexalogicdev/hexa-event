<?php

namespace App\Http\Requests\Organizations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewEventAllowanceRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isSuperadmin() === true; }
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['under_review', 'approve', 'reject'])],
            'approved_quantity' => ['nullable', 'required_if:action,approve', 'integer', 'between:1,100000'],
            'internal_notes' => ['nullable', 'required_if:action,reject', 'string', 'max:5000'],
        ];
    }
}
