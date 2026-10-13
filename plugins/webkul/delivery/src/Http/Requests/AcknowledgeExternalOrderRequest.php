<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcknowledgeExternalOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status'=>['required', Rule::in(['accepted', 'rejected', 'mapping_failed'])], 'edge_order_id'=>['required_if:status,accepted', 'nullable', 'uuid'], 'mapping_errors'=>['sometimes', 'nullable', 'array']];
    }
}
