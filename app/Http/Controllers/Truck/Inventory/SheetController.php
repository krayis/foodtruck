<?php

namespace App\Http\Controllers\Truck\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\InventoryTemplateItems;
use App\InventoryTemplates;
use App\InventorySheets;
use App\InventorySheetItems;
use DateTimeZone;
use DateTime;
use App\Event;
use Illuminate\Support\Facades\DB;

class SheetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $sheets = InventorySheets::where([
            ['truck_id', $user->truck->id]
        ])->get();
        return view('truck.inventory.sheets.index', compact('sheets'));
    }


    public function create()
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        $templates = InventoryTemplates::where([
            ['truck_id', $user->truck->id]
        ])->with('items')->get();
        $template = [];
        foreach ($templates as $temp) {
            $template[$temp->id] = $temp->items->pluck('stock', 'item_id')->toArray();
        }
        $timezone = $user->timezone;
        $carbon = Carbon::now(new DateTimeZone($timezone));
        $offset = $user->tz->gmtOffset();
        $events = Event::where([
                ['user_id', '=', $user->id],
                ['deleted', 0],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->get();
        return view('truck.inventory.sheets.create', compact('items','timezone', 'events', 'templates', 'template'));
    }

    public function edit(InventorySheets $sheet)
    {
        $user = Auth::user();
        $items = Item::where([
            ['truck_id', $user->truck->id],
            ['deleted', 0],
        ])->orderBy('name', 'asc')->get();
        $sheetItems = InventorySheetItems::where([
            ['inventory_sheet_id', $sheet->id],
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

        return view('truck.inventory.sheets.edit', compact('sheet', 'items', 'sheetItems', 'event'));
    }

    public function update(Request $request, InventorySheets $sheet)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $sheet->update([
            'name' => $request->input('name')
        ]);
        $updates = [];
        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                array_push($updates, [
                    'inventory_sheet_id' => $sheet->id,
                    'item_id' => $key,
                    'stock' => $value
                ]);
            }
            InventorySheetItems::upsert($updates, ['inventory_sheet_id', 'item_id'], ['stock']);
        }
        return redirect()->route('admin.inventory.sheets.edit', $sheet->id)->with('success', 'Inventory sheet was successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'items.*' => ['required', 'integer'],
        ]);
        $user = Auth::user();
        $inventory = InventorySheets::create([
            'user_id' => $user->id,
            'truck_id' => $user->truck->id,
            'event_id' => $request->input('event_id'),
            'name' => $request->input('name')
        ]);
        if (is_array($request->input('items'))) {
            foreach ($request->input('items') as $key => $value) {
                InventorySheetItems::create([
                    'inventory_sheet_id' => $inventory->id,
                    'item_id' => $key,
                    'stock' => $value,
                ]);
            }
        }
        return redirect()->route('admin.inventory.sheets.index')->with('success', 'Inventory sheet was successfully created.');
    }

    public function destroy(InventorySheets $sheet) {
        foreach($sheet->items as $item) {
            $item->delete();
        }
        $sheet->delete();
        return redirect()->route('admin.inventory.sheets.index')->with('success', 'Template sheet was successfully deleted.');
    }

}
