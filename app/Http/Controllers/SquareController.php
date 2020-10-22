<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Cookie;
use Ramsey\Uuid\Uuid;
use Square\Exceptions\ApiException;
use Square\SquareClient;
use Square\Models;
use Square\Models\ObtainTokenRequest;
use Illuminate\Http\Request;
use App\PaymentProviders;

class SquareController extends Controller
{
    // Auth codes expire after 5 minutes, one-time use
    // Access tokens expire after 30 days
    // Renew with the refresh token. Refresh tokens do not expire.
    public function oauth()
    {
        $id = config('app.square_app_id');
        $square_oauth_base_url = config('app.square_oauth_base_url');

        // Set the Auth_State cookie with a random md5 string to protect against cross-site request forgery.
        // Auth_State will expire in 60 seconds after the page is loaded.
        $state = md5(time());

        $url = sprintf('%s/oauth2/authorize?client_id=%s&scope=%s&state=%s',
            $square_oauth_base_url,
            $id,
            urlencode('PAYMENTS_READ PAYMENTS_WRITE PAYMENTS_WRITE_ADDITIONAL_RECIPIENTS'),
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

            // When the response_type is "code", the seller clicked Allow
            // and the authorization page returned the auth tokens.
            if ("code" === $request->input('response_type')) {
                // Get the authorization code and use it to call the obtainOAuthToken wrapper function.
                $authorizationCode = $request->input('code');
                if($this->obtainAndSaveAuthToken($authorizationCode, $user->id, $user->truck->id)){
                    return response()->json([
                        'status' => 'SUCCESS'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'ERROR',
                        'error' => 'OBTAIN_TOKEN_FAILED'
                    ]);
                }
            } elseif ($request->input('error') === "access_denied" && $request->input('error_description') === "user_denied") {
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

    private function obtainOAuthToken($authorizationCode, $refresh=false)
    {
        // Initialize Square PHP SDK OAuth API client.
        $apiClient = new SquareClient([
            'environment' => config('app.square_environment'),
        ]);
        $oauthApi = $apiClient->getOAuthApi();
        // Initialize the request parameters for the obtainToken request.
        $body_grantType = $refresh ? 'refresh_token' : 'authorization_code';
        $body = new ObtainTokenRequest(
            config('app.square_app_id'),
            config('app.square_app_secret'),
            $body_grantType
        );
        if(!$refresh){
            $body->setCode($authorizationCode);
        } else {
            $body->setRefreshToken($authorizationCode);
        }
        // Call obtainToken endpoint to get the OAuth tokens.
        try {
            $response = $oauthApi->obtainToken($body);
            if ($response->isError()) {
                $code = $response->getErrors()[0]->getCode();
                $category = $response->getErrors()[0]->getCategory();
                $detail = $response->getErrors()[0]->getDetail();
                throw new Exception("Error Processing Request: obtainToken failed!\n" . $code . "\n" . $category . "\n" . $detail, 1);
            }
        } catch (ApiException $e) {
            error_log($e->getMessage());
            error_log($e->getHttpResponse()->getRawBody());
            throw new Exception("Error Processing Request: obtainToken failed!\n" . $e->getMessage() . "\n" . $e->getHttpResponse()->getRawBody(), 1);
        }
        // Extract the tokens from the response.
        $accessToken = $response->getResult()->getAccessToken();
        $refreshToken = $response->getResult()->getRefreshToken();
        $expiresAt = $response->getResult()->getExpiresAt();
        $merchantId = $response->getResult()->getMerchantId();
        // Return the tokens along with the expiry date/time and merchant ID.
        return array($accessToken, $refreshToken, $expiresAt, $merchantId);
    }

    public function paymentPage(Request $request)
    {
        return view('truck.order.payment');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'nonce' => ['required', 'string', 'min:1']
        ]);

        // TODO query from database given uuid; don't hardcode values
        $paymentDescription = 'Test Description';
        $truck_id = 6;
        $paymentAmountCents = 200;
        $appFeeAmountCents = 50;
        $tipAmountCents = 50;

        $nonce = $request->input('nonce');

        $paymentProvider = PaymentProviders::where([
            'truck_id' => $truck_id
        ])->first();
        if ($paymentProvider === null) {
            return response()->json([
                'status' => 'ERROR',
                'error' => 'NO_PAYMENT_PROVIDER'
            ]);
        }

        if(strtotime($paymentProvider->expires_at) < time()){
            $this->obtainAndSaveAuthToken($paymentProvider->refresh_token, $paymentProvider->user_id, $paymentProvider->truck_id, true);
            $paymentProvider = PaymentProviders::where([
                'truck_id' => $truck_id
            ])->first();
        }

        $apiClient = new SquareClient([
            'environment' => config('app.square_environment'),
            'accessToken' => $paymentProvider->access_token
        ]);
        $paymentsApi = $apiClient->getPaymentsApi();
        $uuid = Uuid::uuid4();
        $body_amountMoney = new Models\Money;
        $body_amountMoney->setAmount($paymentAmountCents);
        $body_amountMoney->setCurrency(Models\Currency::USD);
        $body = new Models\CreatePaymentRequest(
            $nonce,
            $uuid,
            $body_amountMoney
        );
        $body->setAppFeeMoney(new Models\Money);
        $body->getAppFeeMoney()->setAmount($appFeeAmountCents);
        $body->getAppFeeMoney()->setCurrency(Models\Currency::USD);
        if ($tipAmountCents !== null && $tipAmountCents > 0) {
            $body->setTipMoney(new Models\Money);
            $body->getTipMoney()->setAmount($tipAmountCents);
            $body->getTipMoney()->setCurrency(Models\Currency::USD);
        }
        $body->setNote($paymentDescription);
        try {
            $apiResponse = $paymentsApi->createPayment($body);
            if ($apiResponse->isSuccess()) {
                $createPaymentResponse = $apiResponse->getResult();
                if ($createPaymentResponse->getPayment()->getStatus() === 'COMPLETED') {
                    return response()->json([
                        'status' => 'SUCCESS',
                    ]);
                }
            } else {
                $errors = $apiResponse->getErrors();
                return response()->json([
                    'status' => 'ERROR',
                    'error' => $errors
                ]);
            }
        } catch (ApiException $e) {
            return response()->json([
                'status' => 'ERROR',
                'error' => $e->getMessage()
            ]);
        }
        return response()->json([
            'status' => 'ERROR',
            'error' => 'INCOMPLETE'
        ]);
    }

    private function obtainAndSaveAuthToken($authorizationCode, $userId, $truckId, $refresh=false)
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
                    'vendor' => 'square'
                ]);
            } else {
                $provider->update([
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresAt,
                    'merchant_id' => $merchantId,
                    'truck_id' => $truckId,
                    'vendor' => 'square'
                ]);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
