<?php

namespace Webkul\Delivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngestExternalOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['external_order_id'=>['required', 'string', 'max:255'], 'payload'=>['required', 'array'], 'received_at'=>['sometimes', 'date']];
    }
}
