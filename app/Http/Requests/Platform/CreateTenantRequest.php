<?php

namespace App\Http\Requests\Platform;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Stancl\Tenancy\Database\Models\Domain;

class CreateTenantRequest extends FormRequest
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
            'platform_customer_id'       => ['required', 'string', 'max:255'],
            'subdomain'                  => ['required', 'string', 'max:63', 'regex:/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/'],
            'subscription'               => ['required', 'array'],
            'subscription.plan_key'      => ['required', 'string', 'max:100'],
            'subscription.billing_cycle' => ['required', Rule::in(['monthly', 'yearly'])],
            'subscription.starts_at'     => ['required', 'date'],
            'subscription.ends_at'       => ['required', 'date', 'after:subscription.starts_at'],
            'plugins'                    => ['sometimes', 'array', 'max:100'],
            'plugins.*.name'             => ['required', 'string', 'distinct', 'max:100', 'regex:/^[a-z0-9][a-z0-9-]*$/'],
            'plugins.*.author'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugins.*.summary'          => ['sometimes', 'nullable', 'string'],
            'plugins.*.description'      => ['sometimes', 'nullable', 'string'],
            'plugins.*.latest_version'   => ['sometimes', 'nullable', 'string', 'max:100'],
            'plugins.*.license'          => ['sometimes', 'nullable', 'string', 'max:255'],
            'plugins.*.sort'             => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $subdomain = $this->input('subdomain');

            if (! is_string($subdomain)) {
                return;
            }

            $domain = $subdomain.'.'.config('services.platform_api.tenant_root_domain');

            if (Domain::query()->where('domain', $domain)->exists()) {
                $validator->errors()->add('subdomain', 'The subdomain has already been taken.');
            }
        }];
    }
}
