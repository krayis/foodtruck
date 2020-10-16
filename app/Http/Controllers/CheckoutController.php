<?php

namespace App\Http\Controllers;

use App\Modifier;
use Carbon\Carbon;
use App\Truck;
use App\MenuCategory;
use App\Item;
use App\Event;
use App\Location;
use App\Zone;
use App\OrderItem;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Validator;
use App\Services\OrderHelper;
use App\Order;
use App\Timezone;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'event.id' => ['required'],
            'truck.id' => ['required'],
            'cart.*.id' => ['required'],
//            'cart.*.quantity' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->passes()) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid request',
                    'errors' => $validator->errors()
                ]
            ]);
        }

        $truck = Truck::find($request->input('truck.id'));

        if ($truck === null) {
            return response()->json([
                'error' => [
                    'message' => 'Truck can not be found.',
                ]
            ]);
        }

        $event = Event::find($request->input('event.id'));
        if ($event === null) {
            return response()->json([
                'error' => [
                    'message' => 'Truck is not active at this location.',
                ]
            ]);
        }

        $timezone = $truck->user->timezone;
        $now = Carbon::now(new \DateTimeZone($timezone));
        $startDateTime = new Carbon($event->start_date_time);
        $startDateTime->setTimezone($timezone);
        $endDateTime = new Carbon($event->end_date_time);
        $endDateTime->setTimezone($timezone);
        if ($startDateTime->timestamp >= $now->timestamp || $now->timestamp >= $endDateTime->timestamp || $event === null) {
            return response()->json([
                'error' => [
                    'message' => 'Truck is no longer accepting orders at this location.',
                ]
            ]);
        }

        $location = Location::where([
            'id' => $event->location_id,
            'deleted' => 0,
        ])->first();
        if ($location === null) {
            return response()->json([
                'error' => [
                    'message' => 'Truck location can not be located.',
                ]
            ]);
        }

        $cart = $request->input('cart');
        $itemIds = [];
        foreach ($cart as $item) {
            if (!in_array($item['id'], $itemIds)) {
                array_push($itemIds, $item['id']);
            }
        }
        $items = Item::where([
            ['active', 1],
            ['deleted', 0],
            ['truck_id', $truck->id]
        ])->whereIn('id', $itemIds)->get();

        if (count($itemIds) !== $items->count()) {
            return response()->json([
                'error' => [
                    'message' => 'Items in cart are no longer active.',
                ]
            ]);
        }

        foreach ($cart as $orderItem) {
            $item = $items->find($orderItem['id']);
            $orderItemModifiers = [];

            foreach ($orderItem['modifiers'] as $modifier) {
                array_push($orderItemModifiers, [
                    'id' => $modifier['id'],
                    'category_id' => $modifier['categoryId'],
                ]);
            }

            $count = 0;

            foreach ($item->modifierGroups as $modifierCategory) {
                if ($modifierCategory->modifier_category_type_id === 1) {
                    foreach ($orderItemModifiers as $orderItemModifier) {
                        if ($orderItemModifier['category_id'] == $modifierCategory->id) {
                            $count++;
                        }
                    }
                }
                if ($modifierCategory->modifier_category_type_id === 2) {
                    if ($modifierCategory->min === 0 && $modifierCategory->max === 0) {
                        continue;
                    }
                    foreach ($orderItemModifiers as $orderItemModifier) {
                        if ($orderItemModifier['category_id'] == $modifierCategory->id) {
                            $count++;
                        }
                    }
                }
                if ($modifierCategory->modifier_category_type_id === 1 && $count === 0) {
                    return response()->json([
                        'error' => [
                            'message' => 'Item modifier is required.',
                        ]
                    ]);
                }
                if ($modifierCategory->modifier_category_type_id === 2) {
                    $min = $modifierCategory->min;
                    $max = $modifierCategory->max;
                    $valid = true;
                    if ($min > 0 && $min > $count) {
                        $valid = false;
                    }
                    if ($max === 0) {
                        continue;
                    }
                    if ($max > 0 && $max < $count) {
                        $valid = false;
                    }
                    if (!$valid) {
                        return response()->json([
                            'error' => [
                                'message' => 'Invalid number of item modifiers.',
                            ]
                        ]);
                    }
                }
            }
        }

        $order = OrderHelper::create($request);

        return response()->json($order);
    }

    public function update($checkout, Request $request) {
        $order = Order::where([
            ['uuid', $checkout],
        ])->first();
        if ($request->input('pickup_at')) {
            $time = new Carbon($request->input('pickup_at'), $order->truck->user->timezone);
            $time->setTimezone('UTC');
            $request->merge(['pickup_at' => $time->toDateTimeString()]);
        }
        $order->update($request->only(['event_id', 'tip', 'pickup_at']));
        OrderHelper::updateGrandTotal($order);
        return $this->show($checkout);
    }
    public function show($checkout)
    {
        $response = [];
        $order = Order::where([
            ['status', Order::STATUS_CREATED],
            ['uuid', $checkout],
        ])->first();

        $response['order'] = [
            'truck_id' => $order->truck_id,
            'event_id' => $order->event_id,
            'tip' => $order->tip,
            'tax' => $order->tax,
            'sub_total' => $order->sub_total,
            'tax_total' => $order->tax_total,
            'grand_total' => $order->grand_total,
            'status' => Order::STATUS_CREATED
        ];

        $truck = Truck::find($order->truck_id);
        $carbon = Carbon::now(new \DateTimeZone($truck->user->timezone));

        $response['truck'] = [
            'id' => $truck->id,
            'name' => $truck->name,
            'mobile_phone' => $truck->mobile_phone,
            'timezone' => $truck->user->timezone
        ];

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

        $event = Event::select('id', 'user_id', 'truck_id', 'location_id', 'event_type_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['truck_id', $truck->id],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->first();

//        dd($order->event_id);
        if ($event === null) {

        }

        $now = $carbon->format('Y-m-d H:i:s');
        $response['event'] = [
            'id' => $event->id,
            'start_date_time' => $event->start_date_time,
            'end_date_time' => $event->end_date_time,
            'now_date_time' => $now,
        ];

        $location = Location::where([
            ['deleted', 0],
            ['id', $event->location_id]
        ])->first();


        $response['location'] = [
            'name' => $location->name,
            'formatted_address' => $location->formatted_address,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'note' => $location->note,
        ];

        $items = OrderItem::where([
            ['order_id', $order->id]
        ])->get();

        $orderItems = [];
        foreach ($items as $item) {
            $orderItem = [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'note' => $item->note
            ];

            $modifiers = [];
            foreach($item->modifiers as $modifier) {
                array_push($modifiers, [
                    'id' => $modifier->id,
                    'order_id' => $modifier->order_id,
                    'order_item_id' => $modifier->order_item_id,
                    'name' => $modifier->name,
                    'price' => $modifier->price,
                ]);
            }

            $orderItem['modifiers'] = $modifiers;
            array_push($orderItems, $orderItem);
        }

        $response['items'] = $orderItems;

        $schedule = Event::select('id', 'truck_id', 'location_id', 'event_type_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['truck_id', $truck->id],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->with('location')->orderBy('start_date_time', 'asc')->get();

        $events = [];
        foreach($schedule as $event) {
            array_push($events, [
                'id' => $event->id,
                'start_date_time' => $event->start_date_time,
                'end_date_time' => $event->end_date_time,
                'now_date_time' => $now,
                'location' => [
                    'id' => $event->location->id,
                    'name' => $event->location->name,
                    'formatted_address' => $event->location->formatted_address,
                    'note' => $event->location->note,
                    'latitude' => $event->location->latitude,
                    'longitude' => $event->location->longitude,
                ]
            ]);
        }

        $response['events'] = $events;

        return response()->json($response);
    }
}
