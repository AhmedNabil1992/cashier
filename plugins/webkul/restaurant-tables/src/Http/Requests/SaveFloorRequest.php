<?php

namespace Webkul\RestaurantTables\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', Rule::exists('companies', 'id')],
            'code'       => ['required', 'string', 'max:100', Rule::unique('restaurant_floors', 'code')->where('company_id', $this->input('company_id'))->ignore($this->route('floor')?->getKey())],
            'name'       => ['required', 'string', 'max:255'],
            'sort'       => ['sometimes', 'integer', 'min:0'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }
}
