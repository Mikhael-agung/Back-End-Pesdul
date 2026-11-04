<?php

namespace App\Http\Controllers\Api\V1\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuController extends Controller
{
    public function index(): AnonymousResourceCollection { return MenuResource::collection(Menu::with('category')->where('is_active', true)->latest()->paginate(20)); }
    public function show(Menu $menu): MenuResource { return new MenuResource($menu->load('category')); }
}
