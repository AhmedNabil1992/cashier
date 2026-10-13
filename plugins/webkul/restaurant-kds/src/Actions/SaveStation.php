<?php

namespace Webkul\RestaurantKds\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Webkul\RestaurantKds\Models\Station;

class SaveStation
{
    public function execute(array $attributes, ?Station $station = null): Station
    {
        return DB::transaction(function () use ($attributes, $station): Station {
            $station ??= new Station;
            $rules = Arr::pull($attributes, 'routing_rules');

            $station->fill($attributes)->save();

            $ids = [];

            foreach ($rules as $ruleData) {
                $id = Arr::pull($ruleData, 'id');
                $rule = $id ? $station->routingRules()->findOrFail($id) : $station->routingRules()->make();

                $rule->fill($ruleData)->save();
                $ids[] = $rule->getKey();
            }

            $station->routingRules()->whereNotIn('id', $ids)->delete();

            return $station->refresh()->load('routingRules');
        });
    }
}
