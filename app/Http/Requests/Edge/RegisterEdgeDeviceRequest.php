<?php

namespace App\Http\Requests\Edge;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterEdgeDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'  => ['nullable', 'integer', 'exists:companies,id'],
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', Rule::in(['pos', 'kds', 'waiter', 'server', 'integration'])],
            'abilities'   => ['required', 'array', 'min:1', 'max:20'],
            'abilities.*' => ['required', 'string', 'distinct', Rule::in(EdgeDeviceAbilities::all())],
            'expires_at'  => ['nullable', 'date', 'after:now'],
            'metadata'    => ['nullable', 'array', 'max:25'],
        ];
    }
}
