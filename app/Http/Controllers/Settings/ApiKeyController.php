<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiKeyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    /**
     * Create a new API key. The plain text token is returned only in this response.
     */
    public function store(StoreApiKeyRequest $request): JsonResponse
    {
        $user = Auth::user();
        $abilities = [$request->validated('type')];

        $newAccessToken = $user->createToken($request->validated('name'), $abilities);

        return response()->json([
            'token' => $newAccessToken->plainTextToken,
            'name' => $request->validated('name'),
            'type' => $request->validated('type'),
            'message' => __('La clave se ha creado. Guarda el token en un lugar seguro; no se volverá a mostrar.'),
        ], 201);
    }

    /**
     * Revoke (delete) an API key.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $token = Auth::user()->tokens()->findOrFail($id);
        $token->delete();

        return response()->json([
            'message' => __('La clave API ha sido revocada.'),
        ]);
    }

    /**
     * List the user's API keys (without the token value).
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = Auth::user()->tokens()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'type' => in_array('full_access', $token->abilities ?? []) ? 'full_access' : 'read_only',
                'last_used_at' => $token->last_used_at?->toIso8601String(),
                'created_at' => $token->created_at->toIso8601String(),
            ]);

        return response()->json(['data' => $tokens]);
    }
}
