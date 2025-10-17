<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login page.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        return view('admin.login');
    }

    /**
     * Authenticate the admin user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request){
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->passes()){
            // Attempt to login using the admin guard
            if (Auth::guard('admin')->attempt(['email'=>$request->email, 'password'=>$request->password], $request->get('remember'))) {

                // Get the authenticated admin user
                $role = Auth::guard('admin')->user();

                // Check if the user has the correct admin role (role == 2)
                if ($role->role == 2) {
                    return redirect()->route('admin.dashboard');
                }
                else {
                    // Logout if the user is not authorized for admin panel
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access the admin panel.');
                }
            }
            else {
                // Failed login attempt
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is incorrect!');
            }
        }
        else {
            // Return validation errors and old email input
            return redirect()->route('admin.login')
                             ->withErrors($validator)
                             ->withInputs($request->only('email'));
        }
    }

}
