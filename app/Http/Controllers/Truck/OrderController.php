<?php

namespace App\Http\Controllers\Truck;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\Auth;
use App\Timezone;
use App\Zone;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $user = Auth::user();

        $zone = Zone::where([
            'zone_name' => $user->timezone
        ])->first();

        $timezone = Timezone::where([
                ['zone_id', $zone->zone_id],
                ['time_start', '<=', DB::raw('UNIX_TIMESTAMP(UTC_TIMESTAMP())')],
            ])
            ->orderBy('time_start', 'DESC')
            ->first();

        $offset = gmdate("h:i", abs($timezone->gmt_offset));
        $offset = $timezone->gmt_offset < 0 ? '-' . $offset : '+' . $offset;
        $orders = Order::select('*', DB::raw("CONVERT_TZ(pickup_at, '+00:00' , '". $offset ."') as pickup_at"))->with('items', 'items.modifiers')->orderBy('pickup_at', 'desc')->paginate(20);

        return view('truck.order.index', compact('orders'));
    }
}
