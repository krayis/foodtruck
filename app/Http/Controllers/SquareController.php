<?php

namespace App\Http\Controllers;

use App\UserPayments;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SquareController extends Controller
{
    public function index()
    {
        $id = config('app.square_app_id');
        $secret = config('app.square_app_secret');
        $accessToken = config('app.square_access_token');

        $url = sprintf('https://connect.squareupsandbox.com/oauth2/authorize?client_id=%s&scope=%s',
            $id,
            urlencode('PAYMENTS_READ PAYMENTS_WRITE')
        );
      dd($url);
    }

    public function callback(Request $request)
    {
        $id = config('app.square_app_id');
        $secret = config('app.square_app_secret');
        $accessToken = config('app.square_access_token');

        $api_config = new \SquareConnect\Configuration();
        $api_config->setHost("https://connect.squareupsandbox.com");
        $api_config->setAccessToken($id);
        $api_client = new \SquareConnect\ApiClient($api_config);
        # create an instance of the Location API
        $locations_api = new \SquareConnect\Api\OAuthApi($api_client);
        $body = new \SquareConnect\Model\ObtainTokenRequest();
        $body->setClientId($id);
        $body->setClientSecret($secret);
        $body->setCode($request->input('code'));
        $body->setGrantType('authorization_code');
        try {
            $result = $locations_api->obtainToken($body);
            dd($result);
            UserPayments::create([
                'user_id' => 1,
                'truck_id' => 1,
                'access_token' => $result->getAccessToken(),
                'refresh_token' => $result->getRefreshToken(),
                'expires_at' => $result->getExpiresAt(),
            ]);
        } catch (Exception $e) {
            echo 'Exception when calling OAuthApi->obtainToken: ', $e->getMessage(), PHP_EOL;
        }
    }

}
