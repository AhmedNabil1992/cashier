<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['company_id' => ['required', 'integer', Rule::exists('companies', 'id')], 'provider' => ['required', 'string', 'max:100'], 'external_store_id' => ['required', 'string', 'max:255', Rule::unique('delivery_channels', 'external_store_id')->where('provider', $this->input('provider'))->ignore($this->route('channel')?->getKey())], 'configuration' => ['sometimes', 'nullable', 'array'], 'is_active' => ['sometimes', 'boolean']];
    }
}
