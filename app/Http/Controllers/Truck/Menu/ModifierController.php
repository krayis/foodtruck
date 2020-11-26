<?php

namespace App\Http\Controllers\Truck\Menu;

use Illuminate\Http\Request;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\ModifierGroup;
use App\Modifier;

class ModifierController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $modifiers = Modifier::where([
            ['truck_id', $user->truck->id],
        ])->with('group')->orderBy('sort_order', 'desc')->paginate(20);
        return view('truck.menu.modifier.index', compact( 'modifiers'));
    }

    public function create()
    {
        $user = Auth::user();
        $categories = ModifierGroup::where([
            ['truck_id', $user->truck->id],
        ])->orderBy('name', 'asc')->get();
        return view('truck.menu.modifier.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'type' => ['required', 'in:0,1'],
            'min' => ['nullable', 'required_if:type,1', 'integer'],
            'max' => ['nullable', 'required_if:type,1', 'integer'],
            'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        $user = Auth::user();
        $modifier = Modifier::where('truck_id', $user->truck->id)->orderBy('sort_order', 'desc')->first();

        Modifier::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'modifier_group_id' => $request->input('modifier_group_id'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'sort_order' => $modifier ? $modifier->sort_order + 1 : 0,
        ]);

        return redirect()->route('truck.menu.modifier.index')->with('success', 'Modifier was successfully added.');
    }

    public function edit(Modifier $modifier)
    {
        $user = Auth::user();
        $categories = ModifierGroup::where([
            ['truck_id', $user->truck->id],
        ])->orderBy('name', 'asc')->get();
        return view('truck.menu.modifier.edit', compact('modifier', 'categories'));
    }

    public function update(Request $request, Modifier $modifier)
    {
        $validate = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
            'type' => ['sometimes', 'required', 'in:0,1'],
            'modifier_group_id' => ['sometimes', 'required'],
            'min' => ['nullable', 'required_if:type,1', 'integer'],
            'max' => ['nullable', 'required_if:type,1', 'integer'],
            'price' => ['sometimes', 'required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'active' => ['in:0,1']
        ]);

        $modifier->update($request->only('name', 'type', 'modifier_group_id', 'min', 'max', 'price', 'active'));
        return redirect()->back()->with('success', 'Modifier was successfully updated.');
    }

    public function destroy(Modifier $modifier)
    {
        $modifier->delete();
        return redirect()->back()->with('success', 'Modifier was successfully deleted.');
    }

}
