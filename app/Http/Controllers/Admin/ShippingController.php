<?php

namespace App\Http\Controllers\admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\shipping_charge;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $countries=Country::get();
        $data['countries']=$countries;

        $shippingCharges=shipping_charge::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharges']=$shippingCharges;

        return view('admin.shipping.create',$data);
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
        'country'=>'required',
        'amount'=>'required|numeric'
        ]);
        if ($validator->passes()) {
            $count=Shipping_Charge::where('country_id',$request->country)->count();

            if ($count>0) {
                session()->flash('success','Shipping Already Added.');
                return response()->json([
                    'status'=>true
                    ]);
            }
            $shipping=new shipping_charge();
            $shipping->country_id=$request->country;
            $shipping->charge=$request->amount;
            $shipping->save();
            session()->flash('success','Shipping Added Successfully!');
            return response()->json([
             'status'=>true,
            ]);
        }else{
            return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit($id){
        $shippingCharge=Shipping_Charge::find($id);
        $countries=Country::get();
        $data['countries']=$countries;
        $data['shippingCharge']=$shippingCharge;

        return view('admin.shipping.edit',$data);
    }
    public function update(Request $request,$id){
        $validator=Validator::make($request->all(),[
        'country'=>'required',
        'amount'=>'required|numeric'
        ]);
        if ($validator->passes()) {
            $shipping=shipping_charge::find($id);
            $shipping->country_id=$request->country;
            $shipping->charge=$request->amount;
            $shipping->save();
            session()->flash('success','Shipping Updated Successfully!');
            return response()->json([
             'status'=>true,
            ]);
        }else{
            return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($id){
        $shippingCharge=Shipping_Charge::find($id);
        if ($shippingCharge==null) {
            session()->flash('success','Shipping not found.');
            return response()->json([
                'status'=>true,
            ]);
        }
        $shippingCharge->delete();
        session()->flash('success','Shipping deleted Successfully.');
        return response()->json([
 'status'=>true,
        ]);
    }
}
