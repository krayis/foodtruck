<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\User;
use App\Truck;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->route('merchant.orders.index');
        } else {
            return redirect()->route('merchant.login.index')->with('danger', 'These credentials do not match our records.');
        }

    }

    public function index()
    {
        return view('merchant.login.index');
    }
}
