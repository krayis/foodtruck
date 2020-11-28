<?php

namespace App\Http\Controllers\Merchant;

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
use App\EventType;
use App\Services\LocationManager;

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
        $offset = $user->tz->gmtOffset();

        $locations = Location::where([
            'user_id' => $user->id,
            'deleted' => 0,
        ])->get();

        $event = Event::select('id', 'user_id', 'truck_id', 'location_id', 'type', DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."') as start_date_time"), DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."') as end_date_time"))
            ->where([
                ['user_id', '=', $user->id],
                ['type','IMPROMPTU'],
                [DB::raw("CONVERT_TZ(start_date_time, '+00:00' , '". $offset ."')"), '<=', $carbon->format('Y-m-d H:i:s')],
                [DB::raw("CONVERT_TZ(end_date_time, '+00:00' , '". $offset ."')"), '>=', $carbon->format('Y-m-d H:i:s')],
            ])->first();

        return view('merchant.status.index', compact('carbon', 'user', 'locations', 'range', 'event'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'location_id' => ['required_if:location_type_id,1'],
            'end_date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'location_type_id' => ['required', 'in:1,2,3'],
            'find_me_latitude' => ['nullable', 'required_if:location_type_id,3', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'find_me_longitude' => ['nullable', 'required_if:location_type_id,3', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'place_id' => ['required_if:location_type_id,2'],
        ]);

        if ($request->input('location_type') == 'GPS_COORDINATES') {
            $location = LocationManager::create($user, [
                'name' => 'Find Me Location',
                'latitude' => $request->input('find_me_latitude'),
                'longitude' => $request->input('find_me_longitude'),
            ], null, 'GPS_COORDINATES');
            $locationId = $location->id;
        } else if ($request->input('location_type') == 'PREDETERMINED') {
            $place = LocationManager::getByPlaceId($request->input('place_id'));
            $location = LocationManager::create($user, [
                'name' => $place['result']['name'],
                'formatted_address' => $place['result']['formatted_address'],
                'latitude' => $place['result']['geometry']['location']['lat'],
                'longitude' => $place['result']['geometry']['location']['lng'],
                'geohash' => $place['result']['geohash_encoded']->getGeohash(),
                'payload' => json_encode($place),
            ], $request->input('note'), 'PREDETERMINED');
            $locationId = $location->id;
        } else {
            $locationId = $request->input('location_id');
        }

        $endDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('end_date_time'), $user->timezone);
        $startDateTime = Carbon::now($user->timezone)->sub('1 minute');

        Event::create([
            'user_id' => $user->id,
            'truck_id' => $user->truck->id,
            'location_id' => $locationId,
            'start_date_time' => $startDateTime->tz('UTC')->format('Y-m-d H:i:s'),
            'end_date_time' => $endDateTime->tz('UTC')->format('Y-m-d H:i:s'),
            'type' => 'IMPROMPTU'
        ]);
        return redirect()->route('merchant.status.index')->with('success', 'You have successfully updated your status.');
    }

    public function update(Request $request, Event $status)
    {
        $user = Auth::user();
        if (strtotime($status->start_date_time) <= time() && time() <= strtotime($status->end_date_time)) {
            $endDateTime = Carbon::now($user->timezone);
            $status->update([
                'end_date_time' => $endDateTime->sub('1 minutes')->tz('UTC')->format('Y-m-d H:i:s')
            ]);
        }
        return redirect()->route('merchant.status.index')->with('success', 'You have successfully updated your status.');
    }

}
