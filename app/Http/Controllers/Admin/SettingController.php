<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display the change password form to the admin user.
     */
    public function showChangePasswordForm()
    {
        return view('admin.changePassword'); // Load the change password view
    }

    /**
     * Process the admin password change request.
     */
    public function processChangePassword(Request $request)
    {
        // Validate input fields
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',                // Old password is required
            'new_password' => 'required|min:5',          // New password required with minimum 5 characters
            'confirm_password' => 'required|same:new_password', // Confirm password must match new password
        ]);

        // Get currently logged-in admin user
        $admin = User::where('id', Auth::guard('admin')->user()->id)->first();

        // Check if validation passes
        if ($validator->passes()) {
            // Verify old password matches the stored password
            if (!Hash::check($request->old_password, $admin->password)) {
                session()->flash('error', 'Your old PAssword is incorrect, Plz try another.');
                return response()->json([
                    'status' => true,
                    'message' => 'Your old PAssword is incorrect, Plz try another.'
                ]);
            }

            // Update the password for the admin user
            $adminId = Auth::guard('admin')->user()->id;
            User::where('id', $adminId)->update([
                'password' => Hash::make($request->new_password) // Hash the new password
            ]);

            // Flash success message and return JSON response
            session()->flash('success', 'You have successfully change your password.');
            return response()->json([
                'status' => true,
            ]);
        } 
        else {
            // Return validation errors if any
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
