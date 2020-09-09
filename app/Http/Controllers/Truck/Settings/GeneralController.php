<?php

namespace App\Http\Controllers\Truck\Settings;

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
        return view('truck.settings.general.index', compact('user', 'timezones'));
    }

    public function update(Request $request)
    {
        $timezones = Timezones::get();
        $validate = $request->validate([
            'timezone' => ['required', Rule::in(array_keys($timezones))],
            'name' => ['required'],
            'email' => ['required', 'email'],
            'mobile_phone' => ['required'],
        ]);

        $user = Auth::user();

        $user->truck->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_phone' => $request->input('mobile_phone'),
        ]);

        $user->update([
            'timezone' => $request->input('timezone'),
        ]);
        return redirect()->route('truck.settings.index')->with('success', 'Truck setting was updated successfully');
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
        return redirect()->route('truck.settings.index')->with('success', 'Password was updated successfully');
    }
}
