<?php

namespace App\Http\Controllers\Truck\Menu\Modifier;

use App\Modifier;
use Illuminate\Http\Request;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\ModifierCategory;
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
        $categories = ModifierCategory::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        return view('truck.menu.modifier.category.index', compact( 'categories'));
    }

    public function create()
    {
        return view('truck.menu.modifier.category.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min' => ['nullable', 'required_if:modifier_category_type_id,2', 'integer'],
            'max' => ['nullable', 'required_if:modifier_category_type_id,2', 'integer'],
            'modifier_category_type_id' => ['required', 'integer', Rule::in([1, 2])],
        ]);

        $user = Auth::user();

        ModifierCategory::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'modifier_category_type_id' => $request->input('modifier_category_type_id'),
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'active' => 1,
        ]);

        return redirect()->route('truck.menu.modifier.category.index')->with('success', 'Modifier category was successfully added.');
    }

    public function show(ModifierCategory $category)
    {
        return view('truck.menu.modifier.category.show', compact('category'));
    }
    public function edit(ModifierCategory $category)
    {
        return view('truck.menu.modifier.category.edit', compact('category'));
    }

    public function update(Request $request, ModifierCategory $category)
    {
        $validate = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'min' => ['sometimes', 'nullable', 'required_if:modifier_category_type_id,2', 'integer'],
            'max' => ['sometimes', 'nullable', 'required_if:modifier_category_type_id,2', 'integer'],
            'modifier_category_type_id' => ['sometimes', 'required', 'integer', Rule::in([1, 2])],
            'active' => ['in:0,1'],
        ]);

        $category->update($request->only(['name', 'min', 'max', 'modifier_category_type_id', 'active']));
        return redirect()->route('truck.menu.modifier.category.index')->with('success', 'Modifier category was successfully updated.');
    }

    public function destroy(ModifierCategory $category)
    {
        $category->update([
            'deleted' => 1,
        ]);
        Modifier::where([
            'modifier_category_id' => $category->id
        ])->update([
            'category_id' => null
        ]);
        return redirect()->back()->with('success', 'Modifier category was successfully deleted.');
    }

    public function sort(Request $request, ModifierCategory $category)
    {
        $id = $request->input('id');
        $sortOrder = $request->input('sort_order');
        Modifier::where([
            ['modifier_category_id', $category->id],
            ['sort_order', '<=', $sortOrder],
        ])->update([
            'sort_order' => DB::raw('sort_order - 1'),
        ]);
        Modifier::where([
            'id' => $id
        ])->update([
            'sort_order' => $sortOrder
        ]);
        return response()->json([]);
    }
}
