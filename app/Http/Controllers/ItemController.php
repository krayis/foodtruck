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

class ItemController extends Controller
{
    public function index(Item $item)
    {
        if ($item->active === 0) {
            return response()->json([
                'error' => [
                    'message' => 'Item is no longer available.'
                ]
            ]);
        }

        $carbon = Carbon::now()->tz($item->truck->user->timezone);
        $now = $carbon->format('Y-m-d H:i:s');

        $zone = Zone::where([
            'zone_name' => $item->truck->user->timezone
        ])->first();

        $timezone = Timezone::where([
            ['zone_id', $zone->zone_id],
            ['time_start', '<=', DB::raw('UNIX_TIMESTAMP(UTC_TIMESTAMP())')],
        ])
            ->orderBy('time_start', 'DESC')
            ->first();
        $offset = gmdate("h:i", abs($timezone->gmt_offset));
        $offset = $timezone->gmt_offset < 0 ? '-' . $offset : '+' . $offset;

        $event = Event::select('id', 'location_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '" . $offset . "') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '" . $offset . "') as end_date_time"))->where([
            ['truck_id', $item->truck_id],
            [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '" . $offset . "')"), '<=', $now],
            [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '" . $offset . "')"), '>=', $now],
        ])->first();

        if ($event == null) {
            // @todo event is not longer active
        }

        $response = [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price,
            'thumbnail' => $item->thumbnail,
            'modifier_categories' => [],
            'truck' => [
                'id' => $item->truck->id,
                'name' => $item->truck->name,
            ],
        ];

        foreach ($item->modifierGroups as $modifierCategory) {
            if ($modifierCategory->active !== 1) {
                continue;
            }
            $category = [
                'id' => $modifierCategory->id,
                'name' => $modifierCategory->name,
                'min_permitted' => $modifierCategory->min_permitted,
                'max_permitted' => $modifierCategory->max_permitted,
                'max_permitted_per_option' => $modifierCategory->max_permitted_per_option,
                'type' => $modifierCategory->type,
                'modifiers' => []
            ];

            foreach ($modifierCategory->modifiers as $modifier) {
                if ($modifier->active !== 1) {
                    continue;
                }
                $modifier = [
                    'id' => $modifier->id,
                    'name' => $modifier->name,
                    'price' => $modifier->price,
                    'min' => $modifier->min,
                    'max' => $modifier->max,
                    'type' => $modifier->type,
                ];
                array_push($category['modifiers'], $modifier);
            }
            array_push($response['modifier_categories'], $category);
        }

        return response()->json($response);
    }
}
