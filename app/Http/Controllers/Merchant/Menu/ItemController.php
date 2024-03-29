<?php

namespace App\Http\Controllers\Merchant\Menu;

use App\Modifier;
use App\ModifierGroup;
use Illuminate\Http\Request;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\InventorySheetItems;
use App\InventoryTemplateItems;

class ItemController extends Controller
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
        ])->orderBy('sort_order', 'desc')->get();
        $items = Item::where([
            ['truck_id', $user->truck->id],
        ])->with('modifierGroups', 'category')->orderBy('sort_order', 'desc')->paginate(20);
        return view('merchant.menu.item.index', compact('items', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->get();
        return view('merchant.menu.item.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['max:255'],
            'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'thumbnail' => ['file' => 'max:1024']
        ]);

        $user = Auth::user();
        $item = Item::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->first();

        if ($request->hasfile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = Storage::disk('s3')->put('thumbnails', $file);
        }

        Item::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'category_id' => $request->input('category_id') ? $request->input('category_id') : null,
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'thumbnail' => isset($path) ? $path : null,
            'description' => $request->input('description'),
            'sort_order' => $item ? $item->sort_order - 1 : 0,
            'active' => 1,
        ]);

        return redirect()->action('Truck\Menu\ItemController@index')->with('success', 'Item was successfully added.');
    }

    public function show(Item $item)
    {
        $user = Auth::user();
        $categories = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->get();
        return view('merchant.menu.item.show', compact('item', 'categories'));
    }

    public function edit(Item $item)
    {
        $user = Auth::user();
        $categories = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->get();
        $modifierGroups = ModifierGroup::where([
            ['truck_id', $user->truck->id],
        ])->with('modifiers')->get();
        return view('merchant.menu.item.edit', compact('item', 'categories', 'modifierGroups'));
    }

    public function update(Request $request, Item $item)
    {
        if ($request->input('delete_thumbnail') == 1) {
            Storage::disk('s3')->delete($item->thumbnail);
            $item->thumbnail = null;
            $item->save();
        } else {
            $request->validate([
                'name' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
                'description' => ['max:255'],
                'price' => ['sometimes', 'required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'category_id' => ['nullable', 'integer'],
                'active' => ['in:0,1'],
                'thumbnail' => ['file' => 'max:1024']
            ]);

            if ($request->hasfile('thumbnail')) {
                $file = $request->file('thumbnail');
                $path = Storage::disk('s3')->put('thumbnails', $file);
                $item->update([
                    'thumbnail' => $path,
                ]);
            }

            $item->update($request->only(['name', 'price', 'description', 'category_id', 'active']));

            $item->modifierGroups()->sync($request->input('modifier_groups'));

            if (is_array($request->input('items'))) {
                foreach ($request->input('items') as $category) {
                    ModifierGroup::where([
                        'id' => $category['id']
                    ])->update([
                        'sort_order' => $category['sort_order']
                    ]);
                }
            }
        }

        return redirect()->route('merchant.menu.item.edit', $item->id)->with('success', 'Item was successfully updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        InventorySheetItems::where('item_id', $item->id)->delete();
        InventoryTemplateItems::where('item_id', $item->id)->delete();
        return redirect()->action('Truck\Menu\ItemController@index')->with('success', 'Item was successfully deleted.');
    }
}
