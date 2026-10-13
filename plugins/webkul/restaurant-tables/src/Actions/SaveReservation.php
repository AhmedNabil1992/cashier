<?php

namespace Webkul\RestaurantTables\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\RestaurantTables\Models\Reservation;

class SaveReservation
{
    public function execute(array $attributes, ?Reservation $reservation = null): Reservation
    {
        return DB::transaction(function () use ($attributes, $reservation): Reservation {
            if (($attributes['table_id'] ?? null) !== null && in_array($attributes['status'], ['pending', 'confirmed', 'seated'], true)) {
                $conflict = Reservation::query()->where('table_id', $attributes['table_id'])->whereIn('status', ['pending', 'confirmed', 'seated'])->where('starts_at', '<', $attributes['ends_at'])->where('ends_at', '>', $attributes['starts_at'])->when($reservation, fn ($query) => $query->whereKeyNot($reservation->getKey()))->lockForUpdate()->exists();

                if ($conflict) {
                    throw ValidationException::withMessages(['table_id' => 'The table already has an overlapping reservation.']);
                }
            }

            $reservation ??= new Reservation(['uuid' => (string) Str::uuid()]);
            $reservation->fill($attributes)->save();

            return $reservation->refresh();
        });
    }
}
