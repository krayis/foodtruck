<?php

namespace App\Http\Controllers\Merchant\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\Timezones;
use Illuminate\Validation\Rule;

class GeneralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $timezones = Timezones::get();
        return view('merchant.settings.general.index', compact('user', 'timezones'));
    }

    public function update(Request $request)
    {
        $timezones = Timezones::get();

        $request->validate([
            'timezone' => ['required', Rule::in(array_keys($timezones))],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'mobile_phone' => ['required', 'string', 'min:9', 'max:25'],
        ]);

        $user = Auth::user();

        $mobilPhone = preg_replace('/\D/', '',$request->input('mobile_phone'));

        $user->truck->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_phone' => $mobilPhone,
        ]);

        $user->update([
            'timezone' => $request->input('timezone'),
        ]);

        return redirect()->route('merchant.settings.index')->with('success', 'Truck setting was updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $validate = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!\Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'password' => ['required', 'confirmed'],
        ]);
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
        return redirect()->route('merchant.settings.index')->with('success', 'Password was updated successfully');
    }
}
