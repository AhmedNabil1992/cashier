<?php

namespace Webkul\RestaurantKds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncTicketsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tickets'                 => ['required', 'array', 'min:1', 'max:500'],
            'tickets.*.edge_ticket_id'=> ['required', 'uuid', 'distinct'], 'tickets.*.edge_order_id'=>['required', 'uuid'],
            'tickets.*.station_id'    => ['required', 'integer', Rule::exists('restaurant_kds_stations', 'id')],
            'tickets.*.status'        => ['required', Rule::in(['queued', 'preparing', 'ready', 'served', 'recalled', 'cancelled'])],
            'tickets.*.priority'      => ['sometimes', 'integer', 'min:0', 'max:1000'], 'tickets.*.version'=>['required', 'integer', 'min:1'],
            'tickets.*.payload'       => ['sometimes', 'nullable', 'array'], 'tickets.*.source_created_at'=>['required', 'date'], 'tickets.*.source_updated_at'=>['required', 'date'],
        ];
    }
}
