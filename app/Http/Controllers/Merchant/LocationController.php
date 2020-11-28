<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Location;
use App\LocationCache;
use App\Services\LocationManager;

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

    public function index()
    {
        $user = Auth::user();
        $locations = Location::where([
            'user_id' => $user->id,
            'type' => 'PREDETERMINED',
        ])->paginate(20);
        return view('merchant.location.index', compact('locations'));
    }

    public function create()
    {
        return view('merchant.location.create');
    }

    public function edit(Request $request, Location $location)
    {
        return view('merchant.location.edit', compact('location'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'place_id' => ['required', 'string', 'min:1', 'max:255'],
            'note' => ['max:255'],
        ]);
        $user = Auth::user();
        $place = LocationManager::getByPlaceId($request->input('place_id'));
        LocationManager::create($user, $place, $request->input('note'), 'PREDETERMINED');
        return redirect()->action('Merchant\LocationController@index')->with('success', 'Location was successfully added.');
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'note' => ['max:255'],
        ]);

        $location->update([
            'note' => $request->input('note'),
        ]);

        return redirect()->route('merchant.location.index')->with('success', 'Location was successfully updated.');
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
        return redirect()->route('merchant.location.index')->with('success', 'Location was successfully deleted.');
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
