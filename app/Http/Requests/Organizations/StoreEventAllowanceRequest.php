<?php

namespace App\Http\Requests\Organizations;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventAllowanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $organization = $this->route('organization');
        return $organization instanceof Organization && $this->user()?->can('requestAllowance', $organization);
    }

    public function rules(): array
    {
        return ['requested_quantity' => ['required', 'integer', 'between:1,100000'], 'justification' => ['required', 'string', 'min:10', 'max:5000']];
    }
}
