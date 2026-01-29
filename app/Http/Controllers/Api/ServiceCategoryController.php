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
        $parentOnly = $request->get('parent_only', false);

        $categories = ServiceCategory::query()
            ->when($parentOnly, function ($q) {
                $q->whereNull('parent_id');
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', "%{$query}%")
                        ->orWhere('key', 'like', "%{$query}%")
                        ->orWhere('slug', 'like', "%{$query}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'text' => $category->name.($category->parent ? ' ('.$category->parent->name.')' : ''),
            ]);

        return response()->json([
            'results' => $categories,
        ]);
    }
}
