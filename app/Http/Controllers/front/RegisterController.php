<?php

namespace App\Http\Controllers\front;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    // Show the registration form
    public function index(){
        return view('front.register');
    }

    // Handle the registration form submission
    public function processRegister(Request $request){
        // Validate the incoming request data
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:3', // Name is required and must be at least 3 characters
            'email'=>'required|email|unique:users', // Email is required, must be valid, and unique in users table
            'password'=>'required|min:5|confirmed' // Password is required, min 5 characters, must match password_confirmation field
        ]);

        if ($validator->passes()) {
            // Create a new user instance
            $user=new User();
            $user->name=$request->name; // Set user's name
            $user->email=$request->email; // Set user's email
            $user->phone=$request->phone; // Set user's phone (optional)
            $user->password=Hash::make($request->password); // Hash the password before saving
            $user->save(); // Save user to database

            // Set a flash message for successful registration
            session()->flash('success','You have been registered Successfully!');

            // Return JSON response indicating success
            return response()->json([
                'status'=>true,
            ]);
        } else {
            // Return JSON response with validation errors
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
}
