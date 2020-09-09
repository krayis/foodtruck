<?php

namespace App\Http\Controllers\Truck\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Coupon;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $coupons = Coupon::where([
            ['user_id', $user->id],
            ['deleted', 0],
        ])->get();
        return view('truck.settings.coupon.index', compact('coupons'));
    }

    public function create()
    {
        return view('truck.settings.coupon.create');
    }

    public function edit(Coupon $coupon)
    {
        return view('truck.settings.coupon.edit', compact('coupon'));
    }


    public function store(Request $request)
    {
        $validate = $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'description' => ['max:255'],
            'min' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'type' => ['required', 'integer', "in:0,1"],
            'discount_amount' => ['nullable', 'required_if:type,0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_percentage' => ['nullable', 'required_if:type,1', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        $amount = $request->input('type') == 1 ? $request->input('discount_percentage') : $request->input('discount_amount');
        $user = Auth::user();

        Coupon::create([
            'truck_id' => $user->truck->id,
            'user_id' => $user->id,
            'code' => $request->input('code'),
            'description' => $request->input('description'),
            'min' => $request->input('min'),
            'amount' => $amount,
            'type' => $request->input('type'),
            'active' => 1,
        ]);

        return redirect()->route('truck.settings.coupons.index')->with('success', 'Coupon was successfully added.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validate = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['max:255'],
            'min' => ['sometimes', 'required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'type' => ['sometimes', 'required', 'integer', "in:0,1"],
            'discount_amount' => ['sometimes', 'nullable', 'required_if:type,0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'discount_percentage' => ['sometimes', 'nullable', 'required_if:type,1', 'regex:/^\d+(\.\d{1,2})?$/'],
            'active' => ['in:0,1'],
        ]);

        if ($request->has('type')) {
            $amount = $request->input('type') == 1 ? $request->input('discount_percentage') : $request->input('discount_amount');
            $request->merge(['amount' => $amount]);
        }

        $coupon->update($request->only(['code', 'description', 'min', 'amount', 'type', 'active']));
        return redirect()->route('truck.settings.coupons.index')->with('success', 'Coupon was successfully updated.');
    }

    public function destroy(Request $request, Coupon $coupon)
    {
        $coupon->update([
            'deleted' => 1,
        ]);
        return redirect()->route('truck.settings.coupons.index')->with('success', 'Coupon was successfully deleted.');
    }
}
