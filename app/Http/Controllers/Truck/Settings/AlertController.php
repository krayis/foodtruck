<?php

namespace App\Http\Controllers\Truck\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('truck.settings.alert.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validate = $request->validate([
            'email_notification' => ['email'],
            'mobile_phone' => ['max:255'],
        ]);

        $user->update([
            'mobile_phone' => $request->input('mobile_phone'),
            'email_notification' => $request->input('email_notification'),
        ]);

        return redirect()->route('truck.settings.alerts.index')->with('success', 'Alerts successfully updated');
    }
}
