<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function partner()
    {
        return view('merchant.index');
    }
}
