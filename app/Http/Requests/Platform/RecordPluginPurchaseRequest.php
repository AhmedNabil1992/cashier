<?php

namespace App\Http\Requests\Platform;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecordPluginPurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase_id'                  => ['required', 'string', 'max:255'],
            'payment_reference'            => ['required', 'string', 'max:255'],
            'purchased_at'                 => ['required', 'date'],
            'entitlement'                  => ['required', 'array'],
            'entitlement.starts_at'        => ['required', 'date'],
            'entitlement.ends_at'          => ['required', 'date', 'after:entitlement.starts_at'],
            'plugin'                       => ['required', 'array'],
            'plugin.name'                  => ['required', 'string', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/'],
            'plugin.author'                => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugin.summary'               => ['sometimes', 'nullable', 'string'],
            'plugin.description'           => ['sometimes', 'nullable', 'string'],
            'plugin.latest_version'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'plugin.license'               => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugin.sort'                  => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
