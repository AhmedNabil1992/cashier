<?php

namespace App\Http\Requests\Edge;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveSyncEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'events'                     => ['required', 'array', 'min:1', 'max:500'],
            'events.*.event_id'          => ['required', 'uuid', 'distinct'],
            'events.*.aggregate_type'    => ['required', 'string', 'max:100'],
            'events.*.aggregate_id'      => ['required', 'string', 'max:100'],
            'events.*.aggregate_version' => ['required', 'integer', 'min:1'],
            'events.*.event_type'        => ['required', 'string', 'max:150'],
            'events.*.schema_version'    => ['required', 'integer', 'min:1', 'max:65535'],
            'events.*.occurred_at'       => ['required', 'date'],
            'events.*.payload'           => ['required', 'array'],
        ];
    }
}
