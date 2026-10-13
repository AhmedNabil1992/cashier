<?php

namespace Webkul\RestaurantReports\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestaurantOperationalReport
{
    public function overview(CarbonInterface $from, CarbonInterface $to): array
    {
        return [
            'period'                   => ['from' => $from->toIso8601String(), 'to' => $to->toIso8601String()],
            'qr_orders'                => $this->statusSummary('qr_ordering_order_intents', 'submitted_at', $from, $to, true),
            'kds_tickets'              => $this->statusSummary('restaurant_kds_ticket_projections', 'source_created_at', $from, $to),
            'table_sessions'           => $this->statusSummary('restaurant_table_session_projections', 'source_updated_at', $from, $to),
            'delivery_tasks'           => $this->statusSummary('delivery_task_projections', 'source_updated_at', $from, $to),
            'reservations'             => $this->statusSummary('restaurant_reservations', 'starts_at', $from, $to, true, 'deposit_amount'),
            'external_delivery_orders' => $this->statusSummary('delivery_external_orders', 'received_at', $from, $to),
        ];
    }

    public function daily(CarbonInterface $from, CarbonInterface $to): array
    {
        if (! Schema::hasTable('qr_ordering_order_intents')) {
            return ['available' => false, 'rows' => []];
        }

        $rows = DB::table('qr_ordering_order_intents')->whereBetween('submitted_at', [$from, $to])->get(['status', 'total', 'submitted_at'])
            ->groupBy(fn ($row): string => substr((string) $row->submitted_at, 0, 10))
            ->map(fn ($orders, string $date): array => [
                'date'           => $date,
                'submitted'      => $orders->count(),
                'accepted'       => $orders->where('status', 'accepted')->count(),
                'rejected'       => $orders->where('status', 'rejected')->count(),
                'accepted_total' => round((float) $orders->where('status', 'accepted')->sum('total'), 4),
            ])->sortKeys()->values()->all();

        return ['available' => true, 'rows' => $rows];
    }

    private function statusSummary(string $table, string $dateColumn, CarbonInterface $from, CarbonInterface $to, bool $includeTotal = false, string $totalColumn = 'total'): array
    {
        if (! Schema::hasTable($table)) {
            return ['available' => false, 'count' => 0, 'by_status' => []];
        }

        $query = DB::table($table)->whereBetween($dateColumn, [$from, $to]);
        $byStatus = (clone $query)->select('status', DB::raw('COUNT(*) as aggregate'))->groupBy('status')->pluck('aggregate', 'status')->map(fn ($count): int => (int) $count)->all();
        $result = ['available' => true, 'count' => array_sum($byStatus), 'by_status' => $byStatus];

        if ($includeTotal) {
            $result['total'] = round((float) (clone $query)->sum($totalColumn), 4);
        }

        return $result;
    }
}
