<?php

namespace Webkul\QrOrdering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idempotency_key'               => ['required', 'string', 'max:100'],
            'catalog_version'               => ['required', 'integer', 'min:1'],
            'order_type'                    => ['required', Rule::in(['takeaway', 'dine_in', 'delivery'])],
            'customer'                      => ['sometimes', 'nullable', 'array'],
            'customer.name'                 => ['sometimes', 'nullable', 'string', 'max:255'],
            'customer.phone'                => ['sometimes', 'nullable', 'string', 'max:50'],
            'notes'                         => ['sometimes', 'nullable', 'string', 'max:2000'],
            'items'                         => ['required', 'array', 'min:1', 'max:100'],
            'items.*.menu_item_id'          => ['required', 'integer'],
            'items.*.quantity'              => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.modifier_option_ids'   => ['sometimes', 'array'],
            'items.*.modifier_option_ids.*' => ['integer', 'distinct'],
        ];
    }
}
