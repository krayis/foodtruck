<?php

namespace App\Http\Controllers\Merchant\Menu;

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
            ['truck_id', $user->truck->id]
        ])->orderBy('sort_order', 'desc')->paginate(20);
        return view('merchant.menu.category.index', compact('categories'));
    }

    public function create()
    {
        return view('merchant.menu.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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

        return redirect()->route('merchant.menu.category.index')->with('success', 'Category was successfully added.');
    }

    public function show(MenuCategory $category)
    {
        return view('merchant.menu.category.show', compact('category'));
    }

    public function edit(MenuCategory $category)
    {
        return view('merchant.menu.category.edit', compact('category'));
    }

    public function update(Request $request, MenuCategory $category)
    {
        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
            'active' => ['in:0,1'],
            'description' => ['max:255'],
        ]);
        $user = Auth::user();
        $category->update($request->only(['name', 'description', 'active']));
        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $item) {
                Item::where([
                    'id' => $item['id'],
                    'user_id' => $user->id,
                ])->update([
                    'sort_order' => $item['sort_order']
                ]);
            }
        }
        return redirect()->route('merchant.menu.category.show', $category->id)->with('success', 'Category was successfully updated.');
    }

    public function destroy(MenuCategory $category)
    {
        $categoryId = $category->id;
        $category->delete();
        Item::where([
            'category_id' => $categoryId
        ])->update([
            'category_id' => null
        ]);
        return redirect()->route('merchant.menu.category.index')->with('success', 'Category was successfully deleted.');
    }


}
