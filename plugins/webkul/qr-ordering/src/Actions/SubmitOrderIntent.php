<?php

namespace Webkul\QrOrdering\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\QrOrdering\Models\Channel;
use Webkul\QrOrdering\Models\GuestSession;
use Webkul\QrOrdering\Models\OrderIntent;

class SubmitOrderIntent
{
    public function __construct(public QuoteOrderIntent $quoteOrderIntent) {}

    public function execute(Channel $channel, ?GuestSession $guestSession, array $attributes): OrderIntent
    {
        return DB::transaction(function () use ($channel, $guestSession, $attributes): OrderIntent {
            Channel::query()->lockForUpdate()->findOrFail($channel->getKey());
            $requestHash = hash('sha256', json_encode($attributes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $existing = $channel->orderIntents()->where('idempotency_key', $attributes['idempotency_key'])->first();

            if ($existing) {
                abort_if($existing->request_hash !== $requestHash, 409, 'The idempotency key was already used with a different order.');

                return $existing;
            }

            $quote = $this->quoteOrderIntent->execute($channel, $attributes);

            return $channel->orderIntents()->create([
                'public_id'        => (string) Str::uuid(),
                'guest_session_id' => $guestSession?->getKey(),
                'idempotency_key'  => $attributes['idempotency_key'],
                'request_hash'     => $requestHash,
                'status'           => 'pending_edge',
                'catalog_version'  => $quote['catalog_version'],
                'total'            => $quote['total'],
                'payload'          => $quote,
                'version'          => 1,
                'submitted_at'     => now(),
            ]);
        });
    }
}
