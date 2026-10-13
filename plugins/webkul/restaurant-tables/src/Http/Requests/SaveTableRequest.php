<?php

namespace Webkul\RestaurantTables\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $floor = $this->route('floor');

        return [
            'code'      => ['required', 'string', 'max:100', Rule::unique('restaurant_tables', 'code')->where('floor_id', $floor?->getKey())->ignore($this->route('table')?->getKey())],
            'name'      => ['required', 'string', 'max:255'],
            'capacity'  => ['required', 'integer', 'min:1', 'max:1000'],
            'shape'     => ['required', Rule::in(['square', 'rectangle', 'round', 'custom'])],
            'position'  => ['sometimes', 'nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
