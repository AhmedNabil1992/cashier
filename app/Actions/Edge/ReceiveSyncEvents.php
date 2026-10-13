<?php

namespace App\Actions\Edge;

use App\Models\EdgeDevice;
use App\Models\SyncInboxEvent;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ReceiveSyncEvents
{
    public function execute(EdgeDevice $device, array $events): array
    {
        return DB::transaction(function () use ($device, $events): array {
            $accepted = 0;
            $duplicates = 0;

            foreach ($events as $event) {
                $requestHash = $this->hash($event);
                $now = now();
                $inserted = SyncInboxEvent::query()->insertOrIgnore([
                    ...$event,
                    'source_edge_device_id' => $device->getKey(),
                    'request_hash'          => $requestHash,
                    'status'                => 'received',
                    'payload'               => json_encode($event['payload'], JSON_THROW_ON_ERROR),
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);

                if ($inserted === 1) {
                    $accepted++;

                    continue;
                }

                $existing = SyncInboxEvent::query()->where('event_id', $event['event_id'])->lockForUpdate()->firstOrFail();

                if (! hash_equals($existing->request_hash, $requestHash)
                    || $existing->source_edge_device_id !== $device->getKey()) {
                    throw new ConflictHttpException('The event_id was already used with different event data.');
                }

                $duplicates++;
            }

            return ['accepted' => $accepted, 'duplicates' => $duplicates];
        }, 3);
    }

    private function hash(array $event): string
    {
        return hash('sha256', json_encode($event, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
