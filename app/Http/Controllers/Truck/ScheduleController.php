<?php

namespace App\Http\Controllers\Truck;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Location;
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
            ['event_type_id', EventType::EVENT_SCHEDULED],
            ['deleted', 0],
        ])->with('location')->get();
        $timezone = $user->timezone;
        return view('truck.schedule.index', compact('events', 'timezone'));
    }

    public function create()
    {
        $user = Auth::user();
        $locations = Location::where([
            ['user_id', EventType::EVENT_SCHEDULED],
            ['location_type_id', 1],
        ])->get();
        return view('truck.schedule.create', compact('locations'));
    }

    public function show(Request $request, Event $schedule)
    {
        $user = Auth::user();
        $locations = Location::where([
            ['user_id', $user->id],
            ['location_type_id', 1],
        ])->get();
        $timezone = $user->timezone;
        return view('truck.schedule.show', compact('schedule', 'locations', 'timezone'));
    }

    public function update(Request $request, Event $schedule) {
        $request->validate([
            'date' => ['required',  'date_format:m/d/Y'],
            'start_time' => ['max:255', 'date_format:h:i a'],
            'end_time' => ['required', 'date_format:h:i a'],
            'location' => ['required', 'in:new,save'],
        ]);

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
        $user = Auth::user();

        $startDatetime = date_create_from_format('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('start_time'));
        $endDatetime = date_create_from_format('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('end_time'));

        $schedule->update([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'location_id' => $location_id,
            'event_type_id' => 1,
            'start_date_time' => $startDatetime->format('Y-m-d H:i:s'),
            'end_date_time' => $endDatetime->format('Y-m-d H:i:s'),
        ]);
        return redirect()->route('truck.schedule.index')->with('success', 'Date was successfully updated.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required',  'date_format:m/d/Y'],
            'start_time' => ['max:255', 'date_format:h:i a'],
            'end_time' => ['required', 'date_format:h:i a'],
            'location' => ['required', 'in:new,save'],
        ]);

        $user = Auth::user();
        $startDatetime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('start_time'), $user->timezone);
        $endDatetime = Carbon::createFromFormat('m/d/Y h:i a', $request->input('date') . ' ' . $request->input('end_time'), $user->timezone);

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

        Event::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'location_id' => $location_id,
            'start_date_time' => $startDatetime->tz('UTC')->format('Y-m-d H:i:s'),
            'end_date_time' => $endDatetime->tz('UTC')->format('Y-m-d H:i:s'),
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
