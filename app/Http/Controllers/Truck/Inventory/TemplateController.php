<?php

namespace App\Http\Controllers\Truck\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\InventoryTemplateItems;
use App\InventoryTemplates;
use DateTimeZone;
use DateTime;
use App\Event;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $inventories = InventoryTemplates::where([
            ['truck_id', $user->truck->id],
        ])->get();
        return view('truck.inventory.templates.index', compact('inventories'));
    }


    public function create()
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        return view('truck.inventory.templates.create', compact('items'));
    }

    public function edit(InventoryTemplates $template)
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        $inventoryItems = InventoryTemplateItems::where([
            ['inventory_template_id', $template->id],
        ])->get()->pluck('stock', 'item_id')->toArray();
        $carbon = Carbon::now(new DateTimeZone($user->timezone));
        $offset = $user->tz->gmtOffset();
        $event = Event::select('id', 'user_id', 'truck_id', 'location_id', 'type', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['user_id', '=', $user->id],
                ['type','=', 'PREDETERMINED'],
                [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."')"), '<=', $carbon->format('Y-m-d H:i:s')],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->first();

        return view('truck.inventory.templates.edit', compact('template', 'items', 'inventoryItems', 'event'));
    }

    public function update(Request $request, InventoryTemplates $template)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $template->update([
            'name' => $request->input('name')
        ]);
        $updates = [];
        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                array_push($updates, [
                    'inventory_template_id' => $template->id,
                    'item_id' => $key,
                    'stock' => $value
                ]);
            }
            InventoryTemplateItems::upsert($updates, ['inventory_template_id', 'item_id'], ['stock']);
        }
        return redirect()->route('admin.inventory.templates.edit', $template->id)->with('success', 'Inventory was successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $user = Auth::user();
        $template = InventoryTemplates::create([
            'user_id' => $user->id,
            'truck_id' => $user->truck->id,
            'name' => $request->input('name')
        ]);

        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                InventoryTemplateItems::create([
                    'inventory_template_id' => $template->id,
                    'item_id' => $key,
                    'stock' => $value
                ]);
            }
        }
        return redirect()->route('admin.inventory.templates.index')->with('success', 'Template was successfully created.');

    }

    public function destroy(InventoryTemplates $template) {
        $template->delete();
        return redirect()->route('admin.inventory.templates.index')->with('success', 'Template was successfully deleted.');
    }
}
