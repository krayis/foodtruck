<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use AnthonyMartin\GeoLocation\GeoPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;

class SearchController extends Controller
{
    public function index()
    {
        return view('search/index');
    }

    public function suggestions(Request $request)
    {
        $apiKey = config('app.google_api_key');
        $url = sprintf('https://maps.googleapis.com/maps/api/place/autocomplete/json?input=%s&inputtype=textquery&key=%s',
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
                'structured_formatting' => $address['structured_formatting']['main_text'],
            ];
            array_push($results, $suggestion);
        }
        return response()->json($results);
    }

    public function address(Request $request)
    {
        $apiKey = config('app.google_api_key');
        $url = sprintf('https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s',
            $request->input('place_id'),
            $apiKey
        );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);
        $response = $response->getBody()->getContents();
        $place = json_decode($response, true);

        $cords = new \League\Geotools\Coordinate\Coordinate([$place['result']['geometry']['location']['lat'], ($place['result']['geometry']['location']['lng'])]);
        $geotools = new \League\Geotools\Geotools();
        $encoded = $geotools->geohash()->encode($cords);

        $result = [
            'value' => $place['result']['place_id'],
            'label' => in_array('locality', $place['result']['types']) ? str_replace(', USA', '', $place['result']['formatted_address']) : $place['result']['name'],
            'latitude' => $place['result']['geometry']['location']['lat'],
            'longitude' => $place['result']['geometry']['location']['lng'],
            'geohash' => $encoded->getGeohash(),
        ];
        return response()->json($result);
    }

    public function trucks(Request $request)
    {
        $rules = [
            'latitude' => ['required', 'required_if:location_type_id,3', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ];

        $validator = Validator::make($request->only(['latitude', 'longitude']), $rules);
        if (!$validator->passes()) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid request',
                    'errors' => $validator->errors()
                ]
            ]);
        }

        if (!$request->has('distance') || $request->input('distance') > 100) {
            $distance = 1000;
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $carbon = Carbon::now();
        $now = $carbon->format('Y-m-d H:i:s');

        $events = Event::select(DB::raw("events.id, events.truck_id, locations.name, locations.formatted_address, locations.location_type_id, CAST(haversine(locations.latitude, locations.longitude, ?, ?) AS DECIMAL(10,2)) as distance"))
            ->leftJoin('locations', 'locations.id', '=', 'events.location_id')->where([
                ['start_date_time', '<=', '?'],
                ['end_date_time', '>=', '?'],
                ['events.deleted', '?'],
            ])
            ->groupBy('events.truck_id')
            ->with('truck:name,id')
            ->having('distance', '<=', '?')
            ->orderBy('distance', 'asc')
            ->setBindings([$latitude, $longitude, $now, $now, 0, $distance])->get();

        return response()->json($events);
    }
}
