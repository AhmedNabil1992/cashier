<?php

namespace Webkul\RestaurantKds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $station = $this->route('station');

        return [
            'profile_id'                         => ['required', 'integer', Rule::exists('restaurant_pos_profiles', 'id')],
            'code'                               => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('restaurant_kds_stations', 'code')->where('profile_id', $this->input('profile_id'))->ignore($station?->getKey())],
            'name'                               => ['required', 'string', 'max:255'],
            'display_mode'                       => ['required', Rule::in(['station', 'expo'])],
            'target_seconds'                     => ['required', 'integer', 'min:1', 'max:86400'],
            'settings'                           => ['sometimes', 'nullable', 'array'], 'is_active'=>['sometimes', 'boolean'], 'sort'=>['sometimes', 'integer', 'min:0'],
            'routing_rules'                      => ['required', 'array'],
            'routing_rules.*.id'                 => ['sometimes', 'integer'],
            'routing_rules.*.product_id'         => ['sometimes', 'nullable', 'integer', Rule::exists('products_products', 'id')->where('enable_sales', true)],
            'routing_rules.*.product_category_id'=> ['sometimes', 'nullable', 'integer', Rule::exists('products_categories', 'id')],
            'routing_rules.*.order_type'         => ['sometimes', 'nullable', Rule::in(['takeaway', 'dine_in', 'delivery'])],
            'routing_rules.*.priority'           => ['sometimes', 'integer', 'min:0'], 'routing_rules.*.is_active'=>['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            foreach ($this->input('routing_rules', []) as $index => $rule) {
                if (empty($rule['product_id']) && empty($rule['product_category_id'])) {
                    $validator->errors()->add("routing_rules.{$index}", 'A routing rule must select a product or product category.');
                }
                if (! empty($rule['product_id']) && ! empty($rule['product_category_id'])) {
                    $validator->errors()->add("routing_rules.{$index}", 'A routing rule cannot select both a product and product category.');
                }
            }
        }];
    }
}
