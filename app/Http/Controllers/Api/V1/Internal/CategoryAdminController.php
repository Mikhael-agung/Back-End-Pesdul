<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => MenuCategory::orderBy('name')->get(['id', 'name'])]);
    }

    public function store(Request $request, ActivityLogger $logger): JsonResponse
    {
        $data = $request->validate(
            ['name' => ['required', 'string', 'max:100', 'unique:menu_categories,name']],
            ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Nama kategori sudah ada.'],
        );

        $category = MenuCategory::create($data);

        $logger->log($request->user()->role, 'update_category', "kategori#{$category->id}", [
            'change' => 'create',
            'name' => $category->name,
        ]);

        return response()->json(['data' => $category->only(['id', 'name'])], 201);
    }

    public function update(Request $request, MenuCategory $category, ActivityLogger $logger): JsonResponse
    {
        $data = $request->validate(
            ['name' => ['required', 'string', 'max:100', Rule::unique('menu_categories', 'name')->ignore($category->id)]],
            ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Nama kategori sudah ada.'],
        );

        $old = $category->name;
        $category->update($data);

        if ($old !== $category->name) {
            $logger->log($request->user()->role, 'update_category', "kategori#{$category->id}", [
                'change' => 'rename',
                'from' => $old,
                'to' => $category->name,
            ]);
        }

        return response()->json(['data' => $category->only(['id', 'name'])]);
    }

    public function destroy(Request $request, MenuCategory $category, ActivityLogger $logger): Response
    {
        if ($category->menus()->exists()) {
            throw new BusinessException('Kategori masih punya menu, tidak bisa dihapus.', 'CATEGORY_NOT_EMPTY', 409);
        }

        $category->delete();

        $logger->log($request->user()->role, 'update_category', "kategori#{$category->id}", [
            'change' => 'delete',
            'name' => $category->name,
        ]);

        return response()->noContent();
    }
}