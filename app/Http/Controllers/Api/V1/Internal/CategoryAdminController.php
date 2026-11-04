<?php

namespace App\Http\Controllers\Api\V1\Internal;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index() { return MenuCategory::withCount('menus')->latest()->paginate(); }
    public function store(Request $request) { $data = $request->validate(['name' => ['required','string','max:100']]); return MenuCategory::create($data + ['slug' => Str::slug($data['name'])]); }
}
