<?php

namespace App\Http\Controllers\Truck;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Location;
use App\Services\LocationManager;
use App\Event;
use Carbon\Carbon;
use DateTimeZone;
use DateTime;
use App\EventType;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $events = Event::where([
            ['user_id', $user->id],
            ['type', 'PREDETERMINED'],
        ])->with('location')->paginate(20);
        $timezone = $user->timezone;
        return view('truck.schedule.index', compact('events', 'timezone'));
    }

    public function create()
    {
        $user = Auth::user();
        $locations = Location::where([
            ['user_id', $user->id],
            ['type', 'PREDETERMINED'],
        ])->get();
        return view('truck.schedule.create', compact('locations'));
    }

    public function edit(Event $schedule)
    {
        $user = Auth::user();
        $locations = Location::where([
            ['user_id', $user->id],
            ['type', 'PREDETERMINED'],
        ])->get();
        $timezone = $user->timezone;
        return view('truck.schedule.edit', compact('schedule', 'locations', 'timezone'));
    }

    public function update(Request $request, Event $schedule) {
        $request->validate([
            'date' => ['required',  'date_format:m/d/Y'],
            'start_time' => ['max:255', 'date_format:h:i a'],
            'end_time' => ['required', 'date_format:h:i a'],
            'location' => ['required', 'in:new,save'],
            'preorder' => ['in:1'],
        ]);
        $user = Auth::user();
        $locationId = null;

        if ($request->input('location') === 'save') {
            $locationId = $request->input('location_id');
        }

        if ($request->input('location') === 'new') {
            $place = LocationManager::getByPlaceId($request->input('place_id'));
            $location = LocationManager::create($user, $place, $request->input('note'), 'PREDETERMINED');
            $locationId = $location->id;
        }

        $startDateTime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('start_time'), $user->timezone);
        $endDateTime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('end_time'), $user->timezone);

        $schedule->update([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'location_id' => $locationId,
            'preorder' => $request->input('preorder') ? 1 : 0,
            'start_date_time' => $startDateTime->tz('UTC')->format('Y-m-d H:i:s'),
            'end_date_time' => $endDateTime->tz('UTC')->format('Y-m-d H:i:s'),
        ]);
        return redirect()->route('truck.schedule.edit', $schedule->id)->with('success', 'Date was successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required',  'date_format:m/d/Y'],
            'start_time' => ['max:255', 'date_format:h:i a'],
            'end_time' => ['required', 'date_format:h:i a'],
            'location' => ['required', 'in:new,save'],
            'preorder' => ['required', 'in:0,1'],
        ]);

        $user = Auth::user();

        if ($request->input('location') === 'save') {
            $location_id = $request->input('location_id');
        } else if ($request->input('location') === 'new') {
            $apiKey = config('app.google_api_key');
            $url = sprintf('https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s',
                $request->input('place_id'),
                $apiKey
            );
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);
            $response = $response->getBody()->getContents();
            $place = json_decode($response, true);
            $user = Auth::user();
            $location = Location::create([
                'truck_id' => $user->truck->id,
                'user_id' => $user->id,
                'name' => $place['result']['name'],
                'formatted_address' => $place['result']['formatted_address'],
                'latitude' => $place['result']['geometry']['location']['lat'],
                'longitude' => $place['result']['geometry']['location']['lng'],
                'note' => $request->input('note'),
                'payload' => json_encode($place),
            ]);
            $location_id = $location->id;
        }

        $startDateTime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('start_time'), $user->timezone);
        $endDateTime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('end_time'), $user->timezone);

        Event::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'location_id' => $location_id,
            'type' => 'PREDETERMINED',
            'preorder' => ['in:1'],
            'start_date_time' => $startDateTime->tz('UTC')->format('Y-m-d H:i:s'),
            'end_date_time' => $endDateTime->tz('UTC')->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('truck.schedule.index')->with('success', 'Date was successfully added.');
    }


    public function search(Request $request)
    {
        $apiKey = config('app.google_api_key');
        $url = sprintf('https://maps.googleapis.com/maps/api/place/autocomplete/json?input=%s&inputtype=textquery&fields=photos,formatted_address,name,rating,opening_hours,geometry&key=%s',
            $request->input('term'),
            $apiKey
        );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);
        $suggestions = json_decode($response->getBody()->getContents(), true);
        $results = [];
        foreach ($suggestions['predictions'] as $address) {
            $suggestion = [
                'value' => $address['place_id'],
                'label' => $address['description'],
            ];
            array_push($results, $suggestion);
        }
        return response()->json($results);
    }

    public function destroy(Event $schedule)
    {
        $schedule->update([
            'deleted' => 1
        ]);
        return redirect()->route('truck.schedule.index')->with('success', 'Date was successfully deleted.');
    }

}
