<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    /**
     * Búsqueda de categorías para Select2
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $categories = ServiceCategory::query()
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'text' => $category->name,
            ]);

        return response()->json([
            'results' => $categories,
        ]);
    }
}
