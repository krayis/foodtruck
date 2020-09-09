<?php

namespace App\Http\Controllers\Truck;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuCategory;
use App\Item;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $categories = MenuCategory::where('truck_id', $user->truck->id)->orderBy('sort_order', 'asc')->get();
        return view('truck/menu/index', compact('categories'));
    }

}
