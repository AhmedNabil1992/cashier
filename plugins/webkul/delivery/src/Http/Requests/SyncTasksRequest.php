<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['tasks'=>['required', 'array', 'min:1', 'max:500'], 'tasks.*.edge_task_id'=>['required', 'uuid', 'distinct'], 'tasks.*.edge_order_id'=>['required', 'uuid'], 'tasks.*.driver_id'=>['sometimes', 'nullable', 'integer', Rule::exists('delivery_drivers', 'id')], 'tasks.*.status'=>['required', Rule::in(['unassigned', 'assigned', 'accepted', 'picked_up', 'out_for_delivery', 'delivered', 'failed', 'cancelled'])], 'tasks.*.version'=>['required', 'integer', 'min:1'], 'tasks.*.latitude'=>['sometimes', 'nullable', 'numeric', 'between:-90,90'], 'tasks.*.longitude'=>['sometimes', 'nullable', 'numeric', 'between:-180,180'], 'tasks.*.payload'=>['sometimes', 'nullable', 'array'], 'tasks.*.source_updated_at'=>['required', 'date']];
    }
}
