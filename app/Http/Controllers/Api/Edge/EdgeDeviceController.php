<?php

namespace App\Http\Controllers\Api\Edge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Edge\RegisterEdgeDeviceRequest;
use App\Models\EdgeDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdgeDeviceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(EdgeDevice::query()->latest()->paginate(50));
    }

    public function store(RegisterEdgeDeviceRequest $request): JsonResponse
    {
        $device = EdgeDevice::query()->create($request->validated());
        $token = $device->createToken(
            'edge:'.$device->public_id.':v'.$device->token_version,
            $device->abilities,
            $device->expires_at,
        );

        return response()->json([
            'data'        => $device,
            'plain_token' => $token->plainTextToken,
        ], 201);
    }

    public function rotate(EdgeDevice $edgeDevice): JsonResponse
    {
        $edgeDevice->tokens()->delete();
        $edgeDevice->increment('token_version');
        $edgeDevice->refresh();
        $token = $edgeDevice->createToken(
            'edge:'.$edgeDevice->public_id.':v'.$edgeDevice->token_version,
            $edgeDevice->abilities,
            $edgeDevice->expires_at,
        );

        return response()->json(['data' => $edgeDevice, 'plain_token' => $token->plainTextToken]);
    }

    public function revoke(Request $request, EdgeDevice $edgeDevice): JsonResponse
    {
        $edgeDevice->tokens()->delete();
        $edgeDevice->forceFill(['status' => 'revoked', 'revoked_at' => now()])->save();

        return response()->json(['data' => $edgeDevice->fresh()]);
    }
}
