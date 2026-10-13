<?php

namespace Webkul\RestaurantPos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Webkul\RestaurantPos\Http\Requests\SaveProfileRequest;
use Webkul\RestaurantPos\Http\Resources\ProfileResource;
use Webkul\RestaurantPos\Models\Profile;

class ProfileController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProfileResource::collection(Profile::query()->orderBy('name')->paginate());
    }

    public function store(SaveProfileRequest $request): ProfileResource
    {
        return new ProfileResource(Profile::query()->create($request->validated()));
    }

    public function show(Profile $profile): ProfileResource
    {
        return new ProfileResource($profile);
    }

    public function update(SaveProfileRequest $request, Profile $profile): ProfileResource
    {
        $profile->update($request->validated());

        return new ProfileResource($profile->refresh());
    }
}
