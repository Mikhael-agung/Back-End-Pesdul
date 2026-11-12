<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuController extends Controller
{
    /** Hanya kategori yang punya menu aktif. */
    public function categories(): JsonResponse
    {
        $categories = MenuCategory::whereHas('menus', fn ($q) => $q->active())
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $categories]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $menus = Menu::active()
            ->with(['category', 'todayStock'])
            ->when($request->integer('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = addcslashes($request->string('q')->toString(), '%_\\');

                $q->where('name', 'like', "%{$term}%");
            })
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return MenuResource::collection($menus);
    }

    public function publicSettings(): JsonResponse
    {
        return response()->json(['data' => ['dine_in_full' => Setting::dineInFull()]]);
    }
}