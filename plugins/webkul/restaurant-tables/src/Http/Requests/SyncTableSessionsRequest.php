<?php

namespace Webkul\RestaurantTables\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncTableSessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['sessions'=>['required', 'array', 'min:1', 'max:500'], 'sessions.*.edge_session_id'=>['required', 'uuid', 'distinct'], 'sessions.*.table_id'=>['required', 'integer', Rule::exists('restaurant_tables', 'id')], 'sessions.*.edge_order_id'=>['sometimes', 'nullable', 'uuid'], 'sessions.*.waiter_reference'=>['sometimes', 'nullable', 'string', 'max:255'], 'sessions.*.guest_count'=>['required', 'integer', 'min:1', 'max:1000'], 'sessions.*.status'=>['required', Rule::in(['seated', 'ordering', 'served', 'departed', 'cleaning', 'closed'])], 'sessions.*.version'=>['required', 'integer', 'min:1'], 'sessions.*.payload'=>['sometimes', 'nullable', 'array'], 'sessions.*.source_updated_at'=>['required', 'date']];
    }
}
