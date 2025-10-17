<?php

namespace App\Http\Controllers\admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\shipping_charge;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    /**
     * Show the create shipping charge form with list of countries and existing shipping charges.
     */
    public function create(){
        // Get all countries
        $countries = Country::get();
        $data['countries'] = $countries;

        // Get existing shipping charges along with country names
        $shippingCharges = shipping_charge::select('shipping_charges.*','countries.name')
            ->leftJoin('countries','countries.id','shipping_charges.country_id')
            ->get();
        $data['shippingCharges'] = $shippingCharges;

        // Load the create shipping view
        return view('admin.shipping.create', $data);
    }

    /**
     * Store a new shipping charge for a country.
     */
    public function store(Request $request){
        // Validate input
        $validator = Validator::make($request->all(), [
            'country' => 'required',         // Country must be selected
            'amount' => 'required|numeric'   // Charge must be numeric
        ]);

        if ($validator->passes()) {
            // Check if shipping charge already exists for the country
            $count = Shipping_Charge::where('country_id', $request->country)->count();

            if ($count > 0) {
                session()->flash('success','Shipping Already Added.');
                return response()->json([
                    'status' => true
                ]);
            }

            // Create new shipping charge
            $shipping = new shipping_charge();
            $shipping->country_id = $request->country;
            $shipping->charge = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping Added Successfully!');
            return response()->json([
                'status' => true,
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
     * Show the edit form for a specific shipping charge.
     */
    public function edit($id){
        // Find the shipping charge by ID
        $shippingCharge = Shipping_Charge::find($id);

        // Get all countries for dropdown
        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;

        // Load edit shipping view
        return view('admin.shipping.edit', $data);
    }

    /**
     * Update a specific shipping charge.
     */
    public function update(Request $request, $id){
        // Validate input
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            // Update shipping charge
            $shipping = shipping_charge::find($id);
            $shipping->country_id = $request->country;
            $shipping->charge = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping Updated Successfully!');
            return response()->json([
                'status' => true,
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
     * Delete a specific shipping charge.
     */
    public function destroy($id){
        // Find the shipping charge
        $shippingCharge = Shipping_Charge::find($id);

        if ($shippingCharge == null) {
            // If not found, flash message and return
            session()->flash('success','Shipping not found.');
            return response()->json([
                'status' => true,
            ]);
        }

        // Delete the shipping charge
        $shippingCharge->delete();
        session()->flash('success','Shipping deleted Successfully.');
        return response()->json([
            'status' => true,
        ]);
    }
}
