<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $categories = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->get();
        $itemCount = Item::where('truck_id', $user->truck->id)->count();
        return view('truck/menu/index', compact('categories', 'itemCount'));
    }

    public function store(Request $request) {
        $user = Auth::user();
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
        if (is_array($request->input('categories'))) {
            foreach ($request->input('categories') as $category) {
                MenuCategory::where([
                    'id' => $category['id'],
                    'user_id' => $user->id,
                ])->update([
                    'sort_order' => $category['sort_order']
                ]);
            }
        }
        return redirect()->route('merchant.menu.index')->with('success', 'Menu was successfully updated.');

    }

}
