<?php

namespace Webkul\QrOrdering\Http\Requests;

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
        return [
            'profile_id'           => ['required', 'integer', Rule::exists('restaurant_pos_profiles', 'id')],
            'slug'                 => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('qr_ordering_channels', 'slug')->ignore($this->route('channel')?->getKey())],
            'name'                 => ['required', 'string', 'max:255'],
            'mode'                 => ['required', Rule::in(['browse_only', 'browse_and_order'])],
            'requires_table_token' => ['sometimes', 'boolean'],
            'is_active'            => ['sometimes', 'boolean'],
            'settings'             => ['sometimes', 'nullable', 'array'],
        ];
    }
}
