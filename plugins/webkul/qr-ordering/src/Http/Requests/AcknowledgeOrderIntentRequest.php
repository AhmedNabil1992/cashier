<?php

namespace Webkul\QrOrdering\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcknowledgeOrderIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'           => ['required', Rule::in(['accepted', 'rejected'])],
            'edge_order_id'    => ['required_if:status,accepted', 'nullable', 'uuid'],
            'version'          => ['required', 'integer', 'min:1'],
            'rejection_reason' => ['required_if:status,rejected', 'nullable', 'string', 'max:2000'],
        ];
    }
}
