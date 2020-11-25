<?php


namespace App\Http\Controllers;


use App\PaymentProviders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Stripe\Exception\OAuth\OAuthErrorException;
use Stripe\OAuth;
use Stripe\Stripe;

Stripe::setApiKey(config('app.stripe_app_secret'));

class StripeController
{

    public function oauth()
    {
        $id = config('app.stripe_app_id');
        $oauth_base_url = config('app.stripe_oauth_base_url');

        // Set the Auth_State cookie with a random md5 string to protect against cross-site request forgery.
        // Auth_State will expire in 60 seconds after the page is loaded.
        $state = md5(time());

        $url = sprintf('%s/oauth/authorize?client_id=%s&response_type=code&scope=%s&state=%s&stripe_landing=login',
            $oauth_base_url,
            $id,
            urlencode('read_write'),
            $state
        );

        Cookie::queue('Auth_State', $state, 1);

        return "<a href='$url'>Click here to authorize</a>";
    }

    public function callback(Request $request)
    {
        try {
            // Verify the state to protect against cross-site request forgery.
            if ($request->cookie('Auth_State') !== $request->input('state')) {
                return response()->json([
                    'status' => 'ERROR',
                    'error' => 'AUTH_STATE'
                ]);
            }
            // Verify there is a logged in user
            $user = auth()->user();
            if ($user === null) {
                return response()->json([
                    'status' => 'ERROR',
                    'error' => 'NO_ACTIVE_USER'
                ]);
            }

            // When the response_type is "read_write", the seller clicked Allow
            // and the authorization page returned the auth tokens.
            if ("read_write" === $request->input('scope')) {
                // Get the authorization code and use it to call the obtainOAuthToken wrapper function.
                $authorizationCode = $request->input('code');
                if ($this->obtainAndSaveAuthToken($authorizationCode, $user->id, $user->truck->id)) {
                    return response()->json([
                        'status' => 'SUCCESS'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'ERROR',
                        'error' => 'OBTAIN_TOKEN_FAILED'
                    ]);
                }
            } elseif ($request->input('error') === "access_denied") {
                return response()->json([
                    'status' => 'ERROR',
                    'error' => 'USER_DENIED'
                ]);
            } else {
                return response()->json([
                    'status' => 'ERROR',
                    'error' => $request->input('error') . $request->input('error_description')
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function obtainAndSaveAuthToken($authorizationCode, $userId, $truckId, $refresh = false)
    {
        try {
            list($accessToken, $refreshToken, $expiresAt, $merchantId) = $this->obtainOAuthToken($authorizationCode, $refresh);

            // Create database entry
            $provider = PaymentProviders::where([
                'user_id' => $userId
            ])->first();
            if ($provider === null) {
                PaymentProviders::create([
                    'user_id' => $userId,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresAt,
                    'merchant_id' => $merchantId,
                    'truck_id' => $truckId,
                    'vendor' => 'stripe'
                ]);
            } else {
                $provider->update([
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresAt,
                    'merchant_id' => $merchantId,
                    'truck_id' => $truckId,
                    'vendor' => 'stripe'
                ]);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function obtainOAuthToken($authorizationCode, $refresh = false)
    {
        try {
            $response = $refresh ? OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode
            ]) : OAuth::token([
                'grant_type' => 'refresh_token',
                'refresh_token' => $authorizationCode
            ]);
        } catch (OAuthErrorException $e) {
            return response()->json([
                'status' => 'ERROR',
                'error' => $e->getMessage()
            ]);
        }
        // Extract the tokens from the response.
        $accessToken = $response->access_token;
        $refreshToken = $response->refresh_token;
        $nextYear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1);
        $expiresAt = $nextYear;
        $merchantId = $response->stripe_user_id;
        // Return the tokens along with the expiry date/time and merchant ID.
        return array($accessToken, $refreshToken, $expiresAt, $merchantId);
    }

}
