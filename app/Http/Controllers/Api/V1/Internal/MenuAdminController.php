<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuFormRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Support\Str;

class MenuAdminController extends Controller
{
    public function index() { return MenuResource::collection(Menu::latest()->paginate()); }
    public function store(MenuFormRequest $request): MenuResource { $data = $request->validated(); $data['slug'] = Str::slug($data['name']).'-'.Str::random(5); if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('menus', 'public'); unset($data['image']); return new MenuResource(Menu::create($data)); }
    public function update(MenuFormRequest $request, Menu $menu): MenuResource { $menu->update($request->safe()->except('image')); return new MenuResource($menu->refresh()); }
    public function destroy(Menu $menu): \Illuminate\Http\Response { $menu->delete(); return response()->noContent(); }
}
