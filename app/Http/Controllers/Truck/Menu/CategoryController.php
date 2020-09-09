<?php

namespace App\Http\Controllers\Truck\Menu;

use Illuminate\Http\Request;
use App\MenuCategory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Item;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $categories = MenuCategory::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0]
        ])->orderBy('sort_order', 'desc')->get();
        return view('truck.menu.category.index', compact('categories'));
    }

    public function create()
    {
        return view('truck.menu.category.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['max:255'],
        ]);

        $user = Auth::user();
        $category = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'desc')->first();

        MenuCategory::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'sort_order' => $category ? $category->sort_order + 1 : 0,
            'active' => 1,
        ]);

        return redirect()->route('truck.menu.category.index')->with('success', 'Category was successfully added.');
    }

    public function show(MenuCategory $category)
    {
        return view('truck.menu.category.show', compact('category'));
    }

    public function edit(MenuCategory $category)
    {
        return view('truck.menu.category.edit', compact('category'));
    }

    public function update(Request $request, MenuCategory $category)
    {
        $validate = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
            'active' => ['in:0,1'],
            'description' => ['max:255'],
        ]);
        $category->update($request->only(['name', 'description', 'active']));
        return redirect()->route('truck.menu.category.index')->with('success', 'Category was successfully updated.');
    }

    public function destroy(MenuCategory $category)
    {
        $category->update([
            'deleted' => 1
        ]);
        Item::where([
            'category_id' => $category->id
        ])->update([
            'category_id' => null
        ]);
        return redirect()->back()->with('success', 'Category was successfully deleted.');
    }

    public function sortItem(Request $request, MenuCategory $category)
    {
        $id = $request->input('id');
        $sortOrder = $request->input('sort_order');
        Item::where([
            ['category_id', $category->id],
            ['sort_order', '<=', $sortOrder],
        ])->update([
            'sort_order' => DB::raw('sort_order - 1'),
        ]);
        Item::where([
            'id' => $id
        ])->update([
            'sort_order' => $sortOrder
        ]);
        return response()->json([]);
    }

    public function sortCategory(Request $request)
    {
        $id = $request->input('id');
        $sortOrder = $request->input('sort_order');
        $user = Auth::user();

        MenuCategory::where([
            ['user_id', $user->id],
            ['deleted', 0],
            ['sort_order', '<=', $sortOrder],
        ])->update([
            'sort_order' => DB::raw('sort_order - 1'),
        ]);
        MenuCategory::where([
            'id' => $id
        ])->update([
            'sort_order' => $sortOrder
        ]);
        return response()->json([]);
    }
}
