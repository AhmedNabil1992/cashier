<?php

namespace Webkul\RestaurantTables\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'     => ['required', 'integer', Rule::exists('companies', 'id')],
            'table_id'       => ['sometimes', 'nullable', 'integer', Rule::exists('restaurant_tables', 'id')],
            'guest_name'     => ['required', 'string', 'max:255'],
            'guest_phone'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'party_size'     => ['required', 'integer', 'min:1', 'max:1000'],
            'starts_at'      => ['required', 'date'],
            'ends_at'        => ['required', 'date', 'after:starts_at'],
            'status'         => ['required', Rule::in(['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'])],
            'deposit_amount' => ['sometimes', 'numeric', 'min:0'],
            'notes'          => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
