<?php

namespace Webkul\RestaurantPos\Http\Controllers;

use App\Http\Controllers\Controller;
use Webkul\RestaurantPos\Http\Resources\CatalogPublicationResource;
use Webkul\RestaurantPos\Models\Profile;

class LatestCatalogController extends Controller
{
    public function __invoke(Profile $profile): CatalogPublicationResource
    {
        return new CatalogPublicationResource($profile->publications()->latest('version')->firstOrFail());
    }
}
