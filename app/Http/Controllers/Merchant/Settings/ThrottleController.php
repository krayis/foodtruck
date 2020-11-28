<?php

namespace App\Http\Controllers\Merchant\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ThrottleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('merchant.settings.throttle.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validate = $request->validate([
            'throttle_type' => ['required', 'integer'],
            'throttle_time_slot' => ['required_if:throttle_type,1|required_if:throttle_type,2', 'integer'],
            'throttle_max_orders' => ['required_if:throttle_type,1|required_if:throttle_type,2|required_if:throttle_type,3', 'integer'],
        ]);

        if ($request->input('throttle_type') == 0) {
            $request->merge([
                'throttle_time_slot' => 15,
                'throttle_max_orders' => 1,
            ]);
        }
        $user->truck->update($request->only('throttle_type', 'throttle_time_slot', 'throttle_max_orders'));

        return redirect()->route('merchant.settings.throttle.index')->with('success', 'Throttling successfully updated');
    }
}
