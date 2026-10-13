<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['company_id' => ['required', 'integer', Rule::exists('companies', 'id')], 'name' => ['required', 'string', 'max:255'], 'phone' => ['sometimes', 'nullable', 'string', 'max:50'], 'external_reference' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('delivery_drivers', 'external_reference')->where('company_id', $this->input('company_id'))->ignore($this->route('driver')?->getKey())], 'status' => ['required', Rule::in(['available', 'assigned', 'offline', 'suspended'])], 'is_active' => ['sometimes', 'boolean']];
    }
}
