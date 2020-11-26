<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Location;

class LocationManager
{
    static function getByPlaceId($placeId)
    {
        $url = sprintf('https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s',
            $placeId,
            config('app.google_api_key')
        );
        $client = new Client();
        $response = $client->request('GET', $url);

        $response = $response->getBody()->getContents();
        $location = json_decode($response, true);

        $geotools = new \League\Geotools\Geotools();
        $geohash = new \League\Geotools\Coordinate\Coordinate(sprintf('%s, %s', $location['result']['geometry']['location']['lat'], $location['result']['geometry']['location']['lng']));
        $location['result']['geohash_encoded'] = $geotools->geohash()->encode($geohash, 12);

        return $location;
    }

    static function create($user, $place, $note, $type)
    {
        return Location::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'name' => $place['result']['name'],
            'formatted_address' => $place['result']['formatted_address'],
            'latitude' => $place['result']['geometry']['location']['lat'],
            'longitude' => $place['result']['geometry']['location']['lng'],
            'geohash' => $place['result']['geohash_encoded']->getGeohash(),
            'payload' => json_encode($place),
            'note' => $note,
            'type' => $type,
        ]);
    }

}
