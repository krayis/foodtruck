<?php

namespace App\Http\Controllers\Truck;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\InventoryItem;
use App\Inventory;
use DateTimeZone;
use DateTime;
use App\Event;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $inventories = Inventory::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0]
        ])->get();
        return view('truck.inventory.index', compact('inventories'));
    }


    public function create()
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        return view('truck.inventory.create', compact('items'));
    }

    public function edit(Inventory $inventory)
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        $inventoryItems = InventoryItem::where([
            ['inventory_id', $inventory->id],
        ])->get()->pluck('stock', 'item_id')->toArray();
        $carbon = Carbon::now(new DateTimeZone($user->timezone));
        $offset = $user->tz->gmtOffset();
        $event = Event::select('id', 'user_id', 'truck_id', 'location_id', 'event_type_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['user_id', '=', $user->id],
                ['event_type_id','=', 2],
                [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."')"), '<=', $carbon->format('Y-m-d H:i:s')],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->first();

        return view('truck.inventory.edit', compact('inventory', 'items', 'inventoryItems', 'event'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $inventory->update([
            'name' => $request->input('name')
        ]);
        $updates = [];
        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                array_push($updates, [
                    'inventory_id' => $inventory->id,
                    'item_id' => $key,
                    'stock' => $value
                ]);
            }
            InventoryItem::upsert($updates, ['inventory_id', 'item_id'], ['stock']);
        }
        return redirect()->route('truck.inventory.edit', $inventory->id)->with('success', 'Inventory was successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $user = Auth::user();
        $inventory = Inventory::create([
            'user_id' => $user->id,
            'truck_id' => $user->truck->id,
            'name' => $request->input('name')
        ]);

        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'item_id' => $key,
                    'stock' => $value
                ]);
            }
        }
        return redirect()->route('truck.inventory.index')->with('success', 'Inventory was successfully created.');

    }

}
