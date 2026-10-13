<?php

namespace Webkul\Delivery\Actions;

use Illuminate\Support\Facades\DB;
use Webkul\Delivery\Models\TaskProjection;

class SyncTaskProjections
{
    public function execute(array $tasks): array
    {
        return DB::transaction(function () use ($tasks): array {
            $applied = 0;
            $stale = 0;
            foreach ($tasks as $task) {
                $projection = TaskProjection::query()->where('edge_task_id', $task['edge_task_id'])->lockForUpdate()->first();
                if ($projection && $projection->version >= $task['version']) {
                    $stale++;

                    continue;
                } TaskProjection::query()->updateOrCreate(['edge_task_id'=>$task['edge_task_id']], $task + ['last_synced_at'=>now()]);
                $applied++;
            }

            return compact('applied', 'stale');
        });
    }
}
