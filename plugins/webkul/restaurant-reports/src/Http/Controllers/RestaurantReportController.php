<?php

namespace Webkul\RestaurantReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Webkul\RestaurantReports\Http\Requests\ReportPeriodRequest;
use Webkul\RestaurantReports\Services\RestaurantOperationalReport;

class RestaurantReportController extends Controller
{
    public function overview(ReportPeriodRequest $request, RestaurantOperationalReport $report): JsonResponse
    {
        [$from,$to] = $this->period($request);

        return response()->json(['data'=>$report->overview($from, $to)]);
    }

    public function daily(ReportPeriodRequest $request, RestaurantOperationalReport $report): JsonResponse
    {
        [$from,$to] = $this->period($request);

        return response()->json(['data'=>$report->daily($from, $to)]);
    }

    private function period(ReportPeriodRequest $request): array
    {
        return [Carbon::parse($request->input('from', now()->subDays(29)))->startOfDay(), Carbon::parse($request->input('to', now()))->endOfDay()];
    }
}
