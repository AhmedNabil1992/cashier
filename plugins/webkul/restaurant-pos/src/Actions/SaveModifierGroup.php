<?php

namespace Webkul\RestaurantPos\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\RestaurantPos\Models\ModifierGroup;
use Webkul\RestaurantPos\Models\Profile;

class SaveModifierGroup
{
    /** @param array<string, mixed> $attributes */
    public function execute(Profile $profile, array $attributes, ?ModifierGroup $group = null): ModifierGroup
    {
        return DB::transaction(function () use ($profile, $attributes, $group): ModifierGroup {
            $group ??= new ModifierGroup(['profile_id'=>$profile->getKey()]);
            abort_unless((int) $group->profile_id === (int) $profile->getKey(), 404);
            $options = Arr::pull($attributes, 'options');
            $group->fill($attributes)->save();
            $optionIds = [];

            foreach ($options as $optionData) {
                $optionId = Arr::pull($optionData, 'id');
                $option = $optionId ? $group->options()->findOrFail($optionId) : $group->options()->make();
                $option->fill($optionData)->save();
                $optionIds[] = $option->getKey();
            }

            $group->options()->whereNotIn('id', $optionIds)->delete();
            $this->ensureValidNesting($group);

            return $group->refresh()->load('options');
        });
    }

    private function ensureValidNesting(ModifierGroup $root): void
    {
        $walk = function (ModifierGroup $group, array $path, int $depth) use (&$walk, $root): void {
            if ($depth > 3) {
                throw ValidationException::withMessages(['options'=>'Nested modifier groups cannot exceed three levels.']);
            }

            if (in_array($group->getKey(), $path, true)) {
                throw ValidationException::withMessages(['options'=>'Modifier groups cannot contain a circular reference.']);
            }

            $path[] = $group->getKey();
            $nestedGroups = ModifierGroup::query()
                ->where('profile_id', $root->profile_id)
                ->whereIn('id', $group->options()->whereNotNull('nested_modifier_group_id')->pluck('nested_modifier_group_id'))
                ->get();

            foreach ($nestedGroups as $nestedGroup) {
                $walk($nestedGroup, $path, $depth + 1);
            }
        };

        $walk($root, [], 1);
    }
}
