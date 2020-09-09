<?php

namespace App\Http\Controllers\Truck;

use App\Timezone;
use App\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Location;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Event;

class StatusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $carbon = Carbon::now(new DateTimeZone($user->timezone));
        $now = strtotime($carbon);
        $minutes = date('i', $now);
        $now = $now + ((30 - ($minutes % 15)) * 60);
        $range = range($now, strtotime("24:00"), 15 * 60);

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

        $locations = Location::where([
            'user_id' => $user->id,
            'deleted' => 0,
            'location_type_id' => 1,
        ])->get();

        $event = Event::select('id', 'user_id', 'truck_id', 'location_id', 'event_type_id', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['user_id', '=', $user->id],
                ['event_type_id','=', 2],
                [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."')"), '<=', $carbon->format('Y-m-d H:i:s')],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->first();

        return view('truck.status.index', compact('carbon', 'user', 'locations', 'range', 'event'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validate = $request->validate([
            'location_id' => ['required_if:location_type_id,1'],
            'end_date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'location_type_id' => ['required', 'in:1,2,3'],
            'find_me_latitude' => ['nullable', 'required_if:location_type_id,3', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'find_me_longitude' => ['nullable', 'required_if:location_type_id,3', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'place_id' => ['required_if:location_type_id,2'],
        ]);

        if ($request->input('location_type_id') == 3) {
            $location = Location::create([
                'truck_id' => $user->truck->id,
                'user_id' => $user->id,
                'name' => 'Find Me Location',
                'latitude' => $request->input('find_me_latitude'),
                'longitude' => $request->input('find_me_longitude'),
                'location_type_id' => 3,
            ]);
            $locationId = $location->id;
        } else if ($request->input('location_type_id') == 2) {
            $apiKey = config('app.google_api_key');
            $url = sprintf('https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s',
                $request->input('place_id'),
                $apiKey
            );
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);
            $response = $response->getBody()->getContents();
            $place = json_decode($response, true);
            $location = Location::create([
                'truck_id' => $user->truck->id,
                'user_id' => $user->id,
                'name' => $place['result']['name'],
                'formatted_address' => $place['result']['formatted_address'],
                'latitude' => $place['result']['geometry']['location']['lat'],
                'longitude' => $place['result']['geometry']['location']['lng'],
                'note' => $request->input('note'),
                'payload' => json_encode($place),
                'location_type_id' => 1,
            ]);
            $locationId = $location->id;
        } else {
            $locationId = $request->input('location_id');
        }

        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('end_date_time'), $user->timezone);

        Event::create([
            'user_id' => $user->id,
            'truck_id' => $user->truck->id,
            'location_id' => $locationId,
            'start_date_time' => Date('Y-m-d H:i:s'),
            'end_date_time' => $endDateTime->tz('UTC')->format('Y-m-d H:i:s'),
            'event_type_id' => 2
        ]);
        return redirect()->route('truck.status.index')->with('success', 'You have successfully updated your status.');
    }

    public function update(Request $request, Event $status)
    {
        $user = Auth::user();
        if (strtotime($status->start_date_time) <= time() && time() <= strtotime($status->end_date_time)) {
            $status->update([
                'end_date_time' => date('Y-m-d H:i:s', strtotime($status->start_date_time) - 30)
            ]);
        }
        return redirect()->route('truck.status.index')->with('success', 'You have successfully updated your status.');
    }

}
