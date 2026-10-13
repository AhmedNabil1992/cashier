<?php

namespace Webkul\RestaurantPos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveModifierGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profile = $this->route('profile');
        $modifierGroup = $this->route('modifier_group');

        return [
            'code'                               => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('restaurant_pos_modifier_groups', 'code')->where('profile_id', $profile->getKey())->ignore($modifierGroup?->getKey())],
            'name'                               => ['required', 'string', 'max:255'],
            'minimum_selections'                 => ['required', 'integer', 'min:0'],
            'maximum_selections'                 => ['required', 'integer', 'min:1', 'gte:minimum_selections'],
            'free_allowance'                     => ['sometimes', 'integer', 'min:0', 'lte:maximum_selections'],
            'is_required'                        => ['sometimes', 'boolean'],
            'is_active'                          => ['sometimes', 'boolean'],
            'sort'                               => ['sometimes', 'integer', 'min:0'],
            'options'                            => ['required', 'array', 'min:1'],
            'options.*.id'                       => ['sometimes', 'integer'],
            'options.*.product_id'               => ['sometimes', 'nullable', 'integer', Rule::exists('products_products', 'id')->where('enable_sales', true)],
            'options.*.nested_modifier_group_id' => ['sometimes', 'nullable', 'integer', Rule::exists('restaurant_pos_modifier_groups', 'id')->where('profile_id', $profile->getKey())],
            'options.*.name'                     => ['required', 'string', 'max:255'],
            'options.*.price_delta'              => ['sometimes', 'numeric'],
            'options.*.is_active'                => ['sometimes', 'boolean'],
            'options.*.sort'                     => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->boolean('is_required') && (int) $this->input('minimum_selections') < 1) {
                $validator->errors()->add('minimum_selections', 'A required modifier group must require at least one selection.');
            }
        }];
    }
}
