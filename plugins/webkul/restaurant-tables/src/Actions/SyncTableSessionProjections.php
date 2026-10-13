<?php

namespace Webkul\RestaurantTables\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\RestaurantTables\Models\TableSessionProjection;

class SyncTableSessionProjections
{
    public function execute(array $sessions): array
    {
        return DB::transaction(function () use ($sessions): array {
            $applied = 0;
            $stale = 0;
            foreach ($sessions as $session) {
                $projection = TableSessionProjection::query()->where('edge_session_id', $session['edge_session_id'])->lockForUpdate()->first();
                if ($projection && $projection->version >= $session['version']) {
                    $stale++;

                    continue;
                } TableSessionProjection::query()->updateOrCreate(['edge_session_id'=>$session['edge_session_id']], $session + ['last_synced_at'=>now()]);
                $applied++;
            }

            return compact('applied', 'stale');
        });
    }
}
