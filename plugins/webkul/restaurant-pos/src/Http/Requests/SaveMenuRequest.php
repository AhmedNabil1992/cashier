<?php

namespace Webkul\RestaurantPos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Scopes\CompanyScope;

class SaveMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profile = $this->route('profile');
        $menu = $this->route('menu');

        return [
            'code'                                                       => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/', Rule::unique('restaurant_pos_menus', 'code')->where('profile_id', $profile->getKey())->ignore($menu?->getKey())],
            'name'                                                       => ['required', 'string', 'max:255'],
            'channels'                                                   => ['required', 'array', 'min:1'],
            'channels.*'                                                 => ['required', 'distinct', Rule::in(['pos', 'qr', 'delivery', 'kiosk'])],
            'availability'                                               => ['sometimes', 'nullable', 'array'],
            'is_active'                                                  => ['sometimes', 'boolean'],
            'sort'                                                       => ['sometimes', 'integer', 'min:0'],
            'categories'                                                 => ['required', 'array'],
            'categories.*.id'                                            => ['sometimes', 'integer'],
            'categories.*.product_category_id'                           => ['sometimes', 'nullable', 'integer', Rule::exists('products_categories', 'id')],
            'categories.*.name'                                          => ['required', 'string', 'max:255'],
            'categories.*.description'                                   => ['sometimes', 'nullable', 'string'],
            'categories.*.image'                                         => ['sometimes', 'nullable', 'string', 'max:2048'],
            'categories.*.availability'                                  => ['sometimes', 'nullable', 'array'],
            'categories.*.is_active'                                     => ['sometimes', 'boolean'],
            'categories.*.sort'                                          => ['sometimes', 'integer', 'min:0'],
            'categories.*.items'                                         => ['required', 'array'],
            'categories.*.items.*.id'                                    => ['sometimes', 'integer'],
            'categories.*.items.*.product_id'                            => ['required', 'integer', Rule::exists('products_products', 'id')->where('enable_sales', true)],
            'categories.*.items.*.display_name'                          => ['sometimes', 'nullable', 'string', 'max:255'],
            'categories.*.items.*.description'                           => ['sometimes', 'nullable', 'string'],
            'categories.*.items.*.image'                                 => ['sometimes', 'nullable', 'string', 'max:2048'],
            'categories.*.items.*.price_override'                        => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'categories.*.items.*.availability'                          => ['sometimes', 'nullable', 'array'],
            'categories.*.items.*.is_active'                             => ['sometimes', 'boolean'],
            'categories.*.items.*.sort'                                  => ['sometimes', 'integer', 'min:0'],
            'categories.*.items.*.modifier_groups'                       => ['sometimes', 'array'],
            'categories.*.items.*.modifier_groups.*.id'                  => ['required', 'integer', Rule::exists('restaurant_pos_modifier_groups', 'id')->where('profile_id', $profile->getKey())],
            'categories.*.items.*.modifier_groups.*.sort'                => ['sometimes', 'integer', 'min:0'],
            'categories.*.items.*.prices'                                => ['sometimes', 'array'],
            'categories.*.items.*.prices.*.id'                           => ['sometimes', 'integer'],
            'categories.*.items.*.prices.*.channel'                      => ['sometimes', 'nullable', Rule::in(['pos', 'qr', 'delivery', 'kiosk'])],
            'categories.*.items.*.prices.*.order_type'                   => ['sometimes', 'nullable', Rule::in(['takeaway', 'dine_in', 'delivery'])],
            'categories.*.items.*.prices.*.amount'                       => ['required', 'numeric', 'min:0'],
            'categories.*.items.*.prices.*.starts_at'                    => ['sometimes', 'nullable', 'date'],
            'categories.*.items.*.prices.*.ends_at'                      => ['sometimes', 'nullable', 'date'],
            'categories.*.items.*.prices.*.priority'                     => ['sometimes', 'integer', 'min:0'],
            'categories.*.items.*.prices.*.is_active'                    => ['sometimes', 'boolean'],
            'categories.*.items.*.combo_groups'                          => ['sometimes', 'array'],
            'categories.*.items.*.combo_groups.*.id'                     => ['sometimes', 'integer'],
            'categories.*.items.*.combo_groups.*.code'                   => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/'],
            'categories.*.items.*.combo_groups.*.name'                   => ['required', 'string', 'max:255'],
            'categories.*.items.*.combo_groups.*.minimum_selections'     => ['required', 'integer', 'min:0'],
            'categories.*.items.*.combo_groups.*.maximum_selections'     => ['required', 'integer', 'min:1'],
            'categories.*.items.*.combo_groups.*.sort'                   => ['sometimes', 'integer', 'min:0'],
            'categories.*.items.*.combo_groups.*.choices'                => ['required', 'array', 'min:1'],
            'categories.*.items.*.combo_groups.*.choices.*.id'           => ['sometimes', 'integer'],
            'categories.*.items.*.combo_groups.*.choices.*.product_id'   => ['required', 'integer', Rule::exists('products_products', 'id')->where('enable_sales', true)],
            'categories.*.items.*.combo_groups.*.choices.*.display_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'categories.*.items.*.combo_groups.*.choices.*.price_delta'  => ['sometimes', 'numeric'],
            'categories.*.items.*.combo_groups.*.choices.*.is_default'   => ['sometimes', 'boolean'],
            'categories.*.items.*.combo_groups.*.choices.*.is_active'    => ['sometimes', 'boolean'],
            'categories.*.items.*.combo_groups.*.choices.*.sort'         => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $profile = $this->route('profile');
            $productIds = collect(Arr::dot($this->input('categories', [])))
                ->filter(fn ($value, string $key): bool => str_ends_with($key, '.product_id'))
                ->map(fn ($value): int => (int) $value)
                ->unique();

            $allowedCount = Product::query()
                ->withoutGlobalScope(CompanyScope::class)
                ->whereIn('id', $productIds)
                ->where('enable_sales', true)
                ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $profile->company_id))
                ->count();

            if ($allowedCount !== $productIds->count()) {
                $validator->errors()->add('categories', 'Every menu product must be sellable for the profile company.');
            }
        }];
    }
}
