<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Truck;
use App\MenuCategory;
use App\Item;
use App\Event;
use App\Location;
use App\Zone;
use App\Timezone;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request, $id)
    {
        if (strrpos($id, '-') !== false) {
            $id = substr($id, strrpos($id, '-') + 1);
        }

        $truck = Truck::find($id);

        $response = [
            'name' => $truck->name,
            'mobile_phone' => $truck->mobile_phone,
            'timzeone' => $truck->user->timezone,
            'menu' => []
        ];

        $carbon = Carbon::now()->tz($truck->user->timezone);
        $now = $carbon->format('Y-m-d H:i:s');


        $zone = Zone::where([
            'zone_name' => $truck->user->timezone
        ])->first();

        $timezone = Timezone::where([
                ['zone_id', $zone->zone_id],
                ['time_start', '<=', DB::raw('UNIX_TIMESTAMP(UTC_TIMESTAMP())')],
            ])
            ->orderBy('time_start', 'DESC')
            ->first();
        $offset = gmdate("h:i", abs($timezone->gmt_offset));
        $offset = $timezone->gmt_offset < 0 ? '-' . $offset : '+' . $offset;


        $event = Event::select('id', 'location_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))->where([
            ['truck_id', $truck->id],
            [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."')"), '<=', $now],
            [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $now],
        ])->first();

        if ($event === null) {
            return response()->json([
                'error' => [
                    'errors' => [
                        'messages' => 'No events found',
                    ]
                ],
            ]);
        }
        $response['event'] = $event;

        $location = Location::select('name', 'formatted_address', 'latitude', 'longitude')->where([
            ['id', $event->location_id],
            ['truck_id', $truck->id]
        ])->first();

        $response['location'] = $location;

        $categories = MenuCategory::select('id', 'name', 'description', 'sort_order')
            ->where([
                ['active', 1],
                ['truck_id', $truck->id],
            ])
            ->orderBy('sort_order', 'asc')
            ->get();

        $categoriesId = [];
        foreach ($categories as $category) {
            if ($category->items->count() === 0) {
                continue;
            }
            array_push($categoriesId, $category->id);
        }

        $items = Item::select('id', 'name', 'description', 'price', 'thumbnail', 'sort_order', 'category_id')
            ->where([
                ['active', 1],
                ['truck_id', $truck->id],
            ])
            ->whereIn('category_id', $categoriesId)
            ->orderBy('sort_order', 'desc')->get();

        foreach ($categories as $category) {
            if (!in_array($category->id, $categoriesId)) {
                continue;
            }
            $category = [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'items' => [],
            ];

            foreach ($items as $item) {
                if ($item->category_id === $category['id']) {
                    array_push($category['items'], $item);
                }
            }
            array_push($response['menu'], $category);
        }

        return response()->json($response);
    }
}
