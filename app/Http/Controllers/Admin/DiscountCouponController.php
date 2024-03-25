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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $DiscountCoupons=DiscountCoupon::latest();
        if(!empty($request->get('keyword'))){
          $DiscountCoupons=$DiscountCoupons->where('name','like','%'.$request->get('keyword').'%');
        }
        $DiscountCoupons=$DiscountCoupons->paginate(10);
        return view('admin.coupon.index',compact('DiscountCoupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
          'code'=>'required',
          'type'=>'required',
          'discount_amount'=>'required|numeric',
          'status'=>'required',
        ]);
        if ($validator->passes()) {

            //to check date is greater then current date
            if (!empty($request->starts_at)) {
                $now=Carbon::parse(now());
                $startAt=Carbon::parse($request->starts_at);

                if ($now->gte($startAt)) {
                    return response()->json([
                    'status'=>false,
                    'errors'=>['starts_at'=>'Starts date cannot be less then current date']
                    ]);
                }
            }

            // //to check date is less then starts date
            if (!empty($request->starts_at) && !empty($request->expires_at)) {

                 $startsAt=Carbon::parse($request->starts_at);
                 $expiresAt=Carbon::parse($request->expires_at);

                if ($startAt->gte($expiresAt)) {
                    return response()->json([
                    'status'=>false,
                    'errors'=>['expires_at'=>'expires date cannot be less then starts date']
                    ]);
                }
            }

            $discountCoupon=new DiscountCoupon();
            $discountCoupon->code=$request->code;
            $discountCoupon->name=$request->name;
            $discountCoupon->description=$request->description;
            $discountCoupon->max_uses=$request->max_uses;
            $discountCoupon->max_uses_user=$request->max_uses_user;
            $discountCoupon->type=$request->type;
            $discountCoupon->discount_amount=$request->discount_amount;
            $discountCoupon->min_amount=$request->min_amount;
            $discountCoupon->status=$request->status;
            $discountCoupon->starts_at=$request->starts_at;
            $discountCoupon->expires_at=$request->expires_at;
            $discountCoupon->save();

            $message="Discount Coupon added Successfully.";
            session()->flash('success',$message);
            return response()->json([
             'status'=>true,
             'message'=>$message
            ]);
        }else{
            return response()->json([
      'status'=>false,
      'errors'=>$validator->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $discountCoupon=DiscountCoupon::find($id);
        if (empty($discountCoupon)){
            return redirect()->route('coupon.index');
        }
        return view('admin.coupon.edit',compact('discountCoupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator=Validator::make($request->all(),[
            'code'=>'required',
            'type'=>'required',
            'discount_amount'=>'required|numeric',
            'status'=>'required',
          ]);
          if ($validator->passes()) {

              //to check date is greater then current date
              if (!empty($request->starts_at)) {
                  $now=Carbon::parse(now());
                  $startAt=Carbon::parse($request->starts_at);

                  if ($now->gte($startAt)) {
                      return response()->json([
                      'status'=>false,
                      'errors'=>['starts_at'=>'Starts date cannot be less then current date']
                      ]);
                  }
              }

              // //to check date is less then starts date
              if (!empty($request->starts_at) && !empty($request->expires_at)) {

                   $startsAt=Carbon::parse($request->starts_at);
                   $expiresAt=Carbon::parse($request->expires_at);

                  if ($startAt->gte($expiresAt)) {
                      return response()->json([
                      'status'=>false,
                      'errors'=>['expires_at'=>'expires date cannot be less then starts date']
                      ]);
                  }
              }

              $discountCoupon= DiscountCoupon::find($id);
              $discountCoupon->code=$request->code;
              $discountCoupon->name=$request->name;
              $discountCoupon->description=$request->description;
              $discountCoupon->max_uses=$request->max_uses;
              $discountCoupon->max_uses_user=$request->max_uses_user;
              $discountCoupon->type=$request->type;
              $discountCoupon->discount_amount=$request->discount_amount;
              $discountCoupon->min_amount=$request->min_amount;
              $discountCoupon->status=$request->status;
              $discountCoupon->starts_at=$request->starts_at;
              $discountCoupon->expires_at=$request->expires_at;
              $discountCoupon->save();

              $message="Discount Coupon Updated Successfully.";
              session()->flash('success',$message);
              return response()->json([
               'status'=>true,
               'message'=>$message
              ]);
          }else{
              return response()->json([
        'status'=>false,
        'errors'=>$validator->errors()
              ]);
          }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $discountCoupon=DiscountCoupon::find($id);
        if(empty($discountCoupon)){
           $request->session()->flash('error','Coupon not Found');
           return response()->json([
           'status'=>true,
           'message'=>'Coupon not Found'
           ]);
        }
        $discountCoupon->delete();
        $request->session()->flash('success','Coupon Deleted Successfully');
        return response()->json([
           'status'=>true,
           'message'=>'Coupon Deleted Successfully'
        ]);

    }
}
