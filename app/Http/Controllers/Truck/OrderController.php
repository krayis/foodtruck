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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"),
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

        $orders = Order::select('*', DB::raw("CONVERT_TZ(pickup_at, '+00:00' , '". $offset ."') as pickup_at"))->orderBy('pickup_at', 'desc')->get();

        return view('truck.order.index', compact('orders'));
    }
}
