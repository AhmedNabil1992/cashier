<?php

namespace Webkul\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Webkul\Delivery\Http\Requests\SaveChannelRequest;
use Webkul\Delivery\Http\Resources\ChannelResource;
use Webkul\Delivery\Models\Channel;

class ChannelController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ChannelResource::collection(Channel::query()->orderBy('provider')->paginate());
    }

    public function store(SaveChannelRequest $request): ChannelResource
    {
        return new ChannelResource(Channel::query()->create($request->validated()));
    }

    public function show(Channel $channel): ChannelResource
    {
        return new ChannelResource($channel);
    }

    public function update(SaveChannelRequest $request, Channel $channel): ChannelResource
    {
        $channel->update($request->validated());

        return new ChannelResource($channel->refresh());
    }

    public function destroy(Channel $channel): Response
    {
        abort_if($channel->externalOrders()->exists(), 409, 'A channel with order history cannot be deleted.');
        $channel->delete();

        return response()->noContent();
    }
}
