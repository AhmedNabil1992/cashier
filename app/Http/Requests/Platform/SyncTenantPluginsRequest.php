<?php

namespace App\Http\Requests\Platform;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncTenantPluginsRequest extends FormRequest
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
            'plugins'                  => ['required', 'array', 'min:1', 'max:100'],
            'plugins.*.name'           => ['required', 'string', 'distinct', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/'],
            'plugins.*.author'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugins.*.summary'        => ['sometimes', 'nullable', 'string'],
            'plugins.*.description'    => ['sometimes', 'nullable', 'string'],
            'plugins.*.latest_version' => ['sometimes', 'nullable', 'string', 'max:100'],
            'plugins.*.license'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugins.*.sort'           => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
