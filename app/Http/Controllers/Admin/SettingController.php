<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin.changePassword');
    }
    public function processChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);
        $admin = User::where('id', Auth::guard('admin')->user()->id)->first();
        if ($validator->passes()) {
            if (!Hash::check($request->old_password, $admin->password)) {
                session()->flash('error', 'Your old PAssword is incorrect, Plz try another.');
                return response()->json([
                    'status' => true,
                    'message' => 'Your old PAssword is incorrect, Plz try another.'
                ]);
            }
            $adminId=Auth::guard('admin')->user()->id;
            User::where('id',$adminId)->update([
              'password'=>Hash::make($request->new_password)
            ]);
            session()->flash('success', 'You have successfully change your password.');
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
