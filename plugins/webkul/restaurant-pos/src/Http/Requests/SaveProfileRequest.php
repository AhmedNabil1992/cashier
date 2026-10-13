<?php

namespace Webkul\RestaurantPos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('profile')?->getKey();
        $orderTypes = ['takeaway', 'dine_in', 'delivery'];

        return [
            'company_id'            => ['required', 'integer', Rule::exists('companies', 'id')],
            'code'                  => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('restaurant_pos_profiles', 'code')->ignore($profileId)],
            'name'                  => ['required', 'string', 'max:255'],
            'default_order_type'    => ['required', Rule::in($orderTypes)],
            'enabled_order_types'   => ['required', 'array', 'min:1'],
            'enabled_order_types.*' => ['required', 'distinct', Rule::in($orderTypes)],
            'settings'              => ['sometimes', 'nullable', 'array'],
            'is_active'             => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if (! in_array($this->input('default_order_type'), $this->input('enabled_order_types', []), true)) {
                $validator->errors()->add('default_order_type', 'The default order type must be enabled.');
            }
        }];
    }
}
