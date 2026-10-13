<?php

namespace Webkul\RestaurantKds\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\RestaurantKds\Models\TicketProjection;

class SyncTicketProjections
{
    public function execute(array $tickets): array
    {
        return DB::transaction(function () use ($tickets): array {
            $applied = 0;
            $stale = 0;

            foreach ($tickets as $ticket) {
                $projection = TicketProjection::query()
                    ->where('edge_ticket_id', $ticket['edge_ticket_id'])
                    ->lockForUpdate()
                    ->first();

                if ($projection && $projection->version >= $ticket['version']) {
                    $stale++;

                    continue;
                }

                TicketProjection::query()->updateOrCreate(
                    ['edge_ticket_id' => $ticket['edge_ticket_id']],
                    $ticket + ['last_synced_at' => now()]
                );

                $applied++;
            }

            return compact('applied', 'stale');
        });
    }
}
