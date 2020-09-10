<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use AnthonyMartin\GeoLocation\GeoPoint;
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
        $result = [
            'value' => $place['result']['place_id'],
            'label' => in_array('locality', $place['result']['types']) ? str_replace(', USA', '', $place['result']['formatted_address']) : $place['result']['name'],
            'latitude' => $place['result']['geometry']['location']['lat'],
            'longitude' => $place['result']['geometry']['location']['lng'],
        ];
        return response()->json($result);
    }

    public function trucks(Request $request)
    {
        $radius = 100;
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $lat = 30.3276;
        $long = -81.6556;
        $geopointA = new GeoPoint($lat, $long);
        $boundingBox = $geopointA->boundingBox(100, 'miles');

        $boundingBox->getMaxLatitude();
        $boundingBox->getMaxLongitude();
        $boundingBox->getMinLatitude();
        $boundingBox->getMinLongitude();

        // https://stackoverflow.com/questions/36705355/finding-geohashes-of-certain-length-within-radius-from-a-point
        $geotools = new \League\Geotools\Geotools();
        $nw = new \League\Geotools\Coordinate\Coordinate([$boundingBox->getMaxLatitude(), $boundingBox->getMinLongitude()]);
        $ne = new \League\Geotools\Coordinate\Coordinate([$boundingBox->getMaxLatitude(), $boundingBox->getMaxLongitude()]);
        $sw = new \League\Geotools\Coordinate\Coordinate([$boundingBox->getMinLatitude(), $boundingBox->getMinLongitude()]);
        $se = new \League\Geotools\Coordinate\Coordinate([$boundingBox->getMinLatitude(), $boundingBox->getMaxLongitude()]);
        $center = new \League\Geotools\Coordinate\Coordinate([$lat, $long]);


        $geotools = new \League\Geotools\Geotools();
        $encoded = $geotools->geohash()->encode($nw); // 12 is the default length / precision

        $boundingBox = $encoded->getBoundingBox();
        $southWest   = $boundingBox[0];

        $northEast   = $boundingBox[1];

        $encoded = $geotools->geohash()->encode($northEast);

        return response()->json();
    }
}
