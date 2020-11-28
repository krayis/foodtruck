<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\User;
use App\Truck;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Services\Timezones;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $timezones = Timezones::get();

        $validate = $request->validate([
            'food_truck_name' => ['required', 'string', 'min:3', 'max:255'],
            'first_name' => ['required', 'string', 'min:3', 'max:45'],
            'last_name' => ['required', 'string', 'min:3', 'max:45'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile_phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            'timezone' => ['required', Rule::in(array_keys($timezones))],
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'email_notification' => $request->input('email'),
            'mobile_phone' => $request->input('mobile_phone'),
            'password' => Hash::make($request->input('password')),
            'timezone' => $request->input('timezone'),
        ]);

        $truck = Truck::create([
            'user_id' => $user->id,
            'name' => $request->input('food_truck_name'),
            'email' => $request->input('email'),
            'mobile_phone' => $request->input('mobile_phone'),
            'throttle_type' => 0,
        ]);

        Auth::login($user);
        return redirect()->route('merchant.orders.index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $timezones = Timezones::get();
        return view('merchant.register.index', compact('timezones'));
    }
}
