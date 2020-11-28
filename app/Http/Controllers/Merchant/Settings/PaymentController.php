<?php

namespace App\Http\Controllers\Merchant\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\UserPaymentProvider;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('merchant.settings.payment.index', compact('user'));
    }

    public function update(Request $request, UserPaymentProvider $payment) {
        $validate = $request->validate([
            'email' => ['required', 'email'],
            'sales_tax' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'minimum_order_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_provider' => ['required', Rule::in(['paypal', 'stripe', 'square'])],
        ]);

        $payment->update($request->only(['email', 'sales_tax', 'minimum_order_amount', 'payment_provider']));

        return redirect()->route('merchant.settings.payments.index')->with('success', 'Payment was updated successfully');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'email' => ['required', 'email'],
            'sales_tax' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'minimum_order_amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_provider' => ['required', Rule::in(['paypal', 'stripe', 'square'])],
        ]);
        $user = Auth::user();
        if ($user->paymentProvider()->exists()) {
            return $this->update($request);
        }
        UserPaymentProvider::create([
            'user_id' => $user->id,
            'email' => $request->input('email'),
            'sales_tax' => $request->input('sales_tax'),
            'minimum_order_amount' => $request->input('minimum_order_amount'),
            'payment_provider' => $request->input('payment_provider'),
        ]);


        return redirect()->route('merchant.settings.payments.index')->with('success', 'Payment was updated successfully');
    }
}
