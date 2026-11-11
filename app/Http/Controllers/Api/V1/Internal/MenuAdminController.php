<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuFormRequest;
use App\Http\Resources\AdminMenuResource;
use App\Models\Menu;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class MenuAdminController extends Controller
{
    /** Semua menu, termasuk nonaktif. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $menus = Menu::with(['category', 'todayStock'])
            ->when($request->integer('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return AdminMenuResource::collection($menus);
    }

    public function store(MenuFormRequest $request, ActivityLogger $logger): JsonResponse
    {
        $data = Arr::except($request->validated(), ['photo', 'remove_photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        }

        $menu = Menu::create($data);

        $logger->log($request->user()->role, 'update_menu', "menu#{$menu->id}", [
            'change' => 'create',
            'name' => $menu->name,
        ]);

        return (new AdminMenuResource($menu->fresh(['category', 'todayStock'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(MenuFormRequest $request, Menu $menu, ActivityLogger $logger): AdminMenuResource
    {
        $data = Arr::except($request->validated(), ['photo', 'remove_photo']);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($menu->photo);
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($menu->photo);
            $data['photo'] = null;
        }

        $menu->update($data);

        $changed = array_keys(Arr::except($menu->getChanges(), ['updated_at']));
        if ($changed) {
            $logger->log($request->user()->role, 'update_menu', "menu#{$menu->id}", [
                'change' => 'update',
                'name' => $menu->name,
                'fields' => $changed,
            ]);
        }

        return new AdminMenuResource($menu->load(['category', 'todayStock']));
    }

    private function deletePhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}