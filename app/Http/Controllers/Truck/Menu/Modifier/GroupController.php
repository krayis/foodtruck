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
use App\ModifierGroup;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $categories = ModifierGroup::where([
            ['truck_id', $user->truck->id],
        ])->with('modifiers')->orderBy('name', 'asc')->paginate(20);
        return view('truck.menu.modifier.group.index', compact('categories'));
    }

    public function create()
    {
        return view('truck.menu.modifier.group.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_permitted' => ['nullable', 'required_if:has_custom_range,1', 'integer'],
            'max_permitted' => ['nullable', 'required_if:has_custom_range,1', 'min:0', 'not_in:0', 'integer'],
            'max_permitted_per_option' => ['nullable','required_if:has_max_permitted,1', 'min:0', 'not_in:0', 'integer'],
            'has_max_permitted' => ['nullable', 'boolean'],
            'has_custom_range' => ['nullable', 'boolean'],
            'rule_condition' => ['required', Rule::in(['range', 'exact'])],
        ]);

        $user = Auth::user();

        if ($request->input('has_custom_range') === null && $request->input('has_max_permitted') === null) {
            $request->merge(['rule_condition' => 'optional']);

        }
        if ($request->input('has_max_permitted')) {
            $request->merge(['max_permitted_per_option' => $request->input('max_permitted_per_option')]);
            $request->merge(['rule_condition' => 'optional_max']);
        }
        if ($request->input('has_custom_range')) {
            $request->merge(['max_permitted_per_option' => $request->input('max_permitted')]);
        }

        ModifierGroup::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'rule_condition' => $request->input('rule_condition'),
            'min_permitted' => $request->input('min_permitted'),
            'max_permitted' => $request->input('max_permitted'),
            'max_permitted_per_option' => $request->input('max_permitted_per_option'),
            'has_max_permitted' => $request->input('has_max_permitted') ? 1 : 0,
            'has_custom_range' => $request->input('has_custom_range') ? 1 : 0,
            'active' => 1,
        ]);

        return redirect()->route('truck.menu.modifier.group.index')->with('success', 'Modifier group was successfully added.');
    }

    public function show(ModifierGroup $group)
    {
        return view('truck.menu.modifier.group.show', compact('group'));
    }
    public function edit(ModifierGroup $group)
    {
        return view('truck.menu.modifier.group.edit', compact('group'));
    }

    public function update(Request $request, ModifierGroup $group)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_permitted' => ['nullable', 'required_if:has_custom_range,1', 'integer'],
            'max_permitted' => ['nullable', 'required_if:has_custom_range,1', 'min:0', 'not_in:0', 'integer'],
            'max_permitted_per_option' => ['nullable','required_if:has_max_permitted,1', 'min:0', 'not_in:0', 'integer'],
            'has_max_permitted' => ['nullable', 'boolean'],
            'has_custom_range' => ['nullable', 'boolean'],
            'rule_condition' => ['required', Rule::in(['range', 'exact'])],
        ]);

        $user = Auth::user();

        if ($request->input('has_custom_range') === null && $request->input('has_max_permitted') === null) {
            $request->merge(['rule_condition' => 'optional']);
        }
        if ($request->input('has_max_permitted')) {
            $request->merge(['rule_condition' => 'optional_max']);
        }
        if ($request->input('has_custom_range')) {
            $request->merge(['max_permitted_per_option' => $request->input('max_permitted')]);
        }

        if (is_array($request->input('modifiers'))) {
            foreach ($request->input('modifiers') as $modifiers) {
                Modifier::where([
                    'id' => $modifiers['id'],
                    'user_id' => $user->id,
                ])->update([
                    'sort_order' => $modifiers['sort_order']
                ]);
            }
        }
        $group->update([
            'name' => $request->input('name'),
            'rule_condition' => $request->input('rule_condition'),
            'min_permitted' => $request->input('min_permitted'),
            'max_permitted' => $request->input('max_permitted'),
            'max_permitted_per_option' => $request->input('max_permitted_per_option'),
            'has_max_permitted' => $request->input('has_max_permitted') ? 1 : 0,
            'has_custom_range' => $request->input('has_custom_range') ? 1 : 0,
        ]);
        return redirect()->route('truck.menu.modifier.group.edit', $group->id)->with('success', 'Modifier group was successfully updated.');
    }

    public function destroy(ModifierGroup $group)
    {
        $groupId = $group->id;
        $group->delete();
        Modifier::where([
            'modifier_group_id' => $groupId
        ])->update([
            'modifier_group_id' => null
        ]);
        return redirect()->route('truck.menu.modifier.group.index')->with('success', 'Modifier category was successfully deleted.');
    }

}
