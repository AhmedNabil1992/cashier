<?php

namespace Webkul\RestaurantPos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\RestaurantPos\Actions\SaveMenu;
use Webkul\RestaurantPos\Http\Requests\SaveMenuRequest;
use Webkul\RestaurantPos\Http\Resources\MenuResource;
use Webkul\RestaurantPos\Models\Menu;
use Webkul\RestaurantPos\Models\Profile;

class MenuController extends Controller
{
    public function index(Profile $profile): AnonymousResourceCollection
    {
        return MenuResource::collection($profile->menus()->with('categories.items.modifierGroups', 'categories.items.prices', 'categories.items.comboGroups.choices')->paginate());
    }

    public function store(SaveMenuRequest $request, Profile $profile, SaveMenu $saveMenu): MenuResource
    {
        return new MenuResource($saveMenu->execute($profile, $request->validated()));
    }

    public function show(Profile $profile, Menu $menu): MenuResource
    {
        $this->ensureBelongsToProfile($profile, $menu);

        return new MenuResource($menu->load('categories.items.modifierGroups', 'categories.items.prices', 'categories.items.comboGroups.choices'));
    }

    public function update(SaveMenuRequest $request, Profile $profile, Menu $menu, SaveMenu $saveMenu): MenuResource
    {
        $this->ensureBelongsToProfile($profile, $menu);

        return new MenuResource($saveMenu->execute($profile, $request->validated(), $menu));
    }

    public function destroy(Profile $profile, Menu $menu): Response
    {
        $this->ensureBelongsToProfile($profile, $menu);
        $menu->delete();

        return response()->noContent();
    }

    private function ensureBelongsToProfile(Profile $profile, Menu $menu): void
    {
        abort_unless((int) $menu->profile_id === (int) $profile->getKey(), 404);
    }
}
