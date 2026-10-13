<?php

namespace Webkul\RestaurantPos\Http\Controllers;

use App\Http\Controllers\Controller;
use Webkul\RestaurantPos\Actions\PublishCatalog;
use Webkul\RestaurantPos\Http\Resources\CatalogPublicationResource;
use Webkul\RestaurantPos\Models\Profile;

class PublishCatalogController extends Controller
{
    public function __invoke(Profile $profile, PublishCatalog $publishCatalog): CatalogPublicationResource
    {
        return new CatalogPublicationResource($publishCatalog->execute($profile));
    }
}
