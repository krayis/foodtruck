<?php

namespace App\Http\Controllers\Truck;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;
use App\Location;
use App\LocationCache;

class LocationController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $locations = Location::where([
            'user_id' => $user->id,
            'location_type_id' => 1,
            'deleted' => 0,
        ])->get();
        return view('truck/location/index', compact('locations'));
    }

    public function create()
    {

        //https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=Museum%20of%20Contemporary%20Art%20Australia&inputtype=textquery&fields=photos,formatted_address,name,rating,opening_hours,geometry&key=AIzaSyDEYtVHikZFHuE-2ffWRCc9fVxB5P68h8w
        return view('truck/location/create');
    }

    public function show(Request $request, Location $location)
    {
        return view('truck.location.show', compact('location'));
    }

    public function edit(Request $request, Location $location)
    {
        return view('truck.location.edit', compact('location'));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'place_id' => ['required', 'string', 'min:1', 'max:255'],
            'note' => ['max:255'],
        ]);

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

        $geotools = new \League\Geotools\Geotools();
        $coordToGeohash = new \League\Geotools\Coordinate\Coordinate(sprintf('%s, %s', $place['result']['geometry']['location']['lat'], $place['result']['geometry']['location']['lng']));
        $encoded = $geotools->geohash()->encode($coordToGeohash, 12);

        Location::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'name' => $place['result']['name'],
            'formatted_address' => $place['result']['formatted_address'],
            'latitude' => $place['result']['geometry']['location']['lat'],
            'longitude' => $place['result']['geometry']['location']['lng'],
            'geohash' => $encoded->getGeohash(),
            'note' => $request->input('note'),
            'payload' => json_encode($place),
            'location_type_id' => 1,
        ]);

        return redirect()->action('Truck\LocationController@index')->with('success', 'Location was successfully added.');
    }

    public function update(Request $request, Location $location)
    {
        $validate = $request->validate([
            'note' => ['max:255'],
        ]);

        if ($request->input('place_id')) {
            $apiKey = config('app.google_api_key');
            $url = sprintf('https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s',
                $request->input('place_id'),
                $apiKey
            );
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);
            $response = $response->getBody()->getContents();
            $place = json_decode($response, true);

            $geotools = new \League\Geotools\Geotools();
            $coordToGeohash = new \League\Geotools\Coordinate\Coordinate(sprintf('%s, %s', $place['result']['geometry']['location']['lat'], $place['result']['geometry']['location']['lng']));
            $encoded = $geotools->geohash()->encode($coordToGeohash, 12);

            $location->update([
                'name' => $place['result']['name'],
                'formatted_address' => $place['result']['formatted_address'],
                'latitude' => $place['result']['geometry']['location']['lat'],
                'longitude' => $place['result']['geometry']['location']['lng'],
                'geohash' => $encoded->getGeohash(),
                'note' => $request->input('note'),
                'payload' => json_encode($place),
            ]);
        }

        $location->update([
            'note' => $request->input('note'),
        ]);

        return redirect()->route('truck.location.index')->with('success', 'Location was successfully updated.');
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
                'latitude' => null,
                'longitude' => null,
            ];
            array_push($results, $suggestion);
        }
        return response()->json($results);
    }

    public function destroy(Location $location)
    {
        $location->update([
            'deleted' => 1
        ]);
        return redirect()->route('truck.location.index')->with('success', 'Location was successfully deleted.');
    }

    public function find(Request $request)
    {
        $apiKey = config('app.google_api_key');
        $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&key=%s',
            $request->input('latitude'),
            $request->input('longitude'),
            $apiKey
        );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);
        $response = $response->getBody()->getContents();
        $places = json_decode($response, true);

        $results = [];

        return response()->json($results);
    }

}
