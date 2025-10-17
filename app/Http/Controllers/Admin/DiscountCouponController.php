<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DiscountCouponController extends Controller
{
    /**
     * Display a paginated listing of discount coupons with optional search by name.
     */
    public function index(Request $request)
    {
        $DiscountCoupons = DiscountCoupon::latest();

        // Filter coupons by keyword if provided
        if(!empty($request->get('keyword'))){
            $DiscountCoupons = $DiscountCoupons->where('name','like','%'.$request->get('keyword').'%');
        }

        $DiscountCoupons = $DiscountCoupons->paginate(10);

        return view('admin.coupon.index', compact('DiscountCoupons'));
    }

    /**
     * Show the form for creating a new discount coupon.
     */
    public function create()
    {
        return view('admin.coupon.create');
    }

    /**
     * Store a newly created discount coupon in storage after validation and date checks.
     */
    public function store(Request $request)
    {
        // Validate required fields
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($validator->passes()) {

            // Check start date is not in the past
            if (!empty($request->starts_at)) {
                $now = Carbon::parse(now());
                $startAt = Carbon::parse($request->starts_at);

                if ($now->gte($startAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Starts date cannot be less than current date']
                    ]);
                }
            }

            // Check expiry date is after start date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $startAt = Carbon::parse($request->starts_at);
                $expiresAt = Carbon::parse($request->expires_at);

                if ($startAt->gte($expiresAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires date cannot be less than start date']
                    ]);
                }
            }

            // Create new coupon
            $discountCoupon = new DiscountCoupon();
            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();

            $message = "Discount Coupon added Successfully.";
            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Display the specified resource (not implemented).
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing an existing discount coupon.
     */
    public function edit(string $id)
    {
        $discountCoupon = DiscountCoupon::find($id);

        if (empty($discountCoupon)) {
            return redirect()->route('coupon.index');
        }

        return view('admin.coupon.edit', compact('discountCoupon'));
    }

    /**
     * Update an existing discount coupon after validation and date checks.
     */
    public function update(Request $request, string $id)
    {
        // Validate required fields
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($validator->passes()) {

            // Check start date is not in the past
            if (!empty($request->starts_at)) {
                $now = Carbon::parse(now());
                $startAt = Carbon::parse($request->starts_at);

                if ($now->gte($startAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Starts date cannot be less than current date']
                    ]);
                }
            }

            // Check expiry date is after start date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $startAt = Carbon::parse($request->starts_at);
                $expiresAt = Carbon::parse($request->expires_at);

                if ($startAt->gte($expiresAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expires date cannot be less than start date']
                    ]);
                }
            }

            // Find and update the coupon
            $discountCoupon = DiscountCoupon::find($id);
            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();

            $message = "Discount Coupon Updated Successfully.";
            session()->flash('success', $message);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove a discount coupon from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $discountCoupon = DiscountCoupon::find($id);

        if (empty($discountCoupon)) {
            $request->session()->flash('error', 'Coupon not Found');
            return response()->json([
                'status' => true,
                'message' => 'Coupon not Found'
            ]);
        }

        // Delete coupon
        $discountCoupon->delete();

        $request->session()->flash('success', 'Coupon Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'Coupon Deleted Successfully'
        ]);
    }
}
