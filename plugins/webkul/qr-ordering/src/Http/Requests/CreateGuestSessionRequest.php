<?php

namespace Webkul\QrOrdering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGuestSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_reference'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'expires_in_minutes' => ['sometimes', 'integer', 'min:5', 'max:1440'],
            'metadata'           => ['sometimes', 'nullable', 'array'],
        ];
    }
}
