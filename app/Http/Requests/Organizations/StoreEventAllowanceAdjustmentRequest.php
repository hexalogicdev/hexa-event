<?php

namespace App\Http\Requests\Organizations;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventAllowanceAdjustmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isSuperadmin() === true; }
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'not_in:0', 'between:-100000,100000'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'agreement_reference' => ['nullable', 'string', 'max:100'],
            'effective_date' => ['required', 'date'],
        ];
    }
}
