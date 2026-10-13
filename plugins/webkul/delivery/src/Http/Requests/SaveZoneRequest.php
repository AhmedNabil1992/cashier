<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['company_id' => ['required', 'integer', Rule::exists('companies', 'id')], 'name' => ['required', 'string', 'max:255'], 'geometry' => ['sometimes', 'nullable', 'array'], 'delivery_fee' => ['required', 'numeric', 'min:0'], 'minimum_order' => ['required', 'numeric', 'min:0'], 'is_active' => ['sometimes', 'boolean']];
    }
}
