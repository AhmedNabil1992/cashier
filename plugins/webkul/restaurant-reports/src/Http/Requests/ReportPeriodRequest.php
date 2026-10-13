<?php

namespace Webkul\RestaurantReports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class ReportPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['from'=>['sometimes', 'date'], 'to'=>['sometimes', 'date', 'after_or_equal:from']];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $from = Carbon::parse($this->input('from', now()->subDays(29)));
            $to = Carbon::parse($this->input('to', now()));
            if ($from->diffInDays($to, true) > 366) {
                $validator->errors()->add('to', 'The report period cannot exceed 366 days.');
            }
        }];
    }
}
