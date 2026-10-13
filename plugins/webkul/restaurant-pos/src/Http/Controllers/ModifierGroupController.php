<?php

namespace Webkul\RestaurantPos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\RestaurantPos\Actions\SaveModifierGroup;
use Webkul\RestaurantPos\Http\Requests\SaveModifierGroupRequest;
use Webkul\RestaurantPos\Http\Resources\ModifierGroupResource;
use Webkul\RestaurantPos\Models\ModifierGroup;
use Webkul\RestaurantPos\Models\Profile;

class ModifierGroupController extends Controller
{
    public function index(Profile $profile): AnonymousResourceCollection
    {
        return ModifierGroupResource::collection($profile->modifierGroups()->with('options')->paginate());
    }

    public function store(SaveModifierGroupRequest $request, Profile $profile, SaveModifierGroup $save): ModifierGroupResource
    {
        return new ModifierGroupResource($save->execute($profile, $request->validated()));
    }

    public function show(Profile $profile, ModifierGroup $modifierGroup): ModifierGroupResource
    {
        $this->ensureBelongsToProfile($profile, $modifierGroup);

        return new ModifierGroupResource($modifierGroup->load('options'));
    }

    public function update(SaveModifierGroupRequest $request, Profile $profile, ModifierGroup $modifierGroup, SaveModifierGroup $save): ModifierGroupResource
    {
        $this->ensureBelongsToProfile($profile, $modifierGroup);

        return new ModifierGroupResource($save->execute($profile, $request->validated(), $modifierGroup));
    }

    public function destroy(Profile $profile, ModifierGroup $modifierGroup): Response
    {
        $this->ensureBelongsToProfile($profile, $modifierGroup);
        abort_if($modifierGroup->menuItems()->exists(), 409, 'The modifier group is assigned to menu items.');
        $modifierGroup->delete();

        return response()->noContent();
    }

    private function ensureBelongsToProfile(Profile $profile, ModifierGroup $modifierGroup): void
    {
        abort_unless((int) $modifierGroup->profile_id === (int) $profile->getKey(), 404);
    }
}
