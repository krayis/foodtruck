<?php


namespace App\Http\Controllers\PaymentProviders;


// https://developer.paypal.com/docs/checkout/integrate/#

use Illuminate\Http\Request;

class PayPalController
{

    public function paymentPage(Request $request)
    {
        return view('truck.order.paypalPayment');
    }

    private static function buildRequestBody()
    {
//        order = http.post(PAYPAL_ORDER_API, {
        //    headers: {
        //        Accept:        `application/json`,
        //      Authorization: `Bearer ${ auth.access_token }`
        //    },
        //    data: {
        //        intent: 'CAPTURE',
        //      purchase_units: [{
        //            amount: {
        //                currency_code: 'USD',
        //          value: '220.00'
        //        },
        //            payee: {
        //                email_address: 'payee@email.com'
        //        }
        //        }]
        //    }
        //  });
        return array(
            'intent' => 'CAPTURE',
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'amount' =>
                                array(
                                    'currency_code' => 'USD',
                                    'value' => '220.00'
                                ),
                            'payee' =>
                                array(
                                    'email_address' => 'payee@email.com'
                                )
                        )
                )
        );
    }

}
