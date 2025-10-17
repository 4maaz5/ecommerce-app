<?php

namespace App\Http\Controllers\front;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\WishList;
use App\Models\Order_item;
use App\Models\Customer_Address;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /** Show login page */
    public function index() {
        return view('front.login');
    }

    /** Handle login form submission */
    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $remember = $request->get('remembered') ?? false;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            return redirect()->intended(route('account.profile'));
        }

        session()->flash('error', 'Either email or password is incorrect.');
        return redirect()->route('front.login')->withInput($request->only('email'));
    }

    /** Show user profile */
    public function profile() {
        $user = Auth::user();
        $countries = Country::orderBy('name')->get();
        $address = Customer_Address::where('user_id', $user->id)->first();

        return view('front.account.profile', [
            'user' => $user,
            'countries' => $countries,
            'address' => $address
        ]);
    }

    /** Logout user */
    public function logout() {
        Auth::logout();
        return redirect()->route('front.login')->with('success', 'You have successfully logged out.');
    }

    /** List user orders */
    public function orders() {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('front.account.orders', ['orders' => $orders]);
    }

    /** Show order details */
    public function orderDetail($orderId) {
        $order = Order::where('user_id', Auth::id())
            ->where('id', $orderId)
            ->firstOrFail();

        $orderItems = Order_item::where('order_id', $orderId)->get();

        return view('front.account.orderDetail', [
            'order' => $order,
            'orderItems' => $orderItems,
            'orderItemsCount' => $orderItems->count()
        ]);
    }

    /** Show user's wishlist */
    public function wishList() {
        $wishLists = WishList::where('user_id', Auth::id())
            ->with('product')
            ->get();

        return view('front.account.wishList', ['wishLists' => $wishLists]);
    }

    /** Remove product from wishlist */
    public function removeProduct(Request $request) {
        $wishList = WishList::where('user_id', Auth::id())
            ->where('product_id', $request->id)
            ->first();

        if (!$wishList) {
            session()->flash('error', 'Product already removed.');
            return response()->json(['status' => true]);
        }

        $wishList->delete();
        session()->flash('success', 'Product removed successfully.');
        return response()->json(['status' => true]);
    }

    /** Update profile info */
    public function updateProfile(Request $request) {
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        session()->flash('success', 'Profile updated successfully.');
        return response()->json(['status' => true, 'message' => 'Profile updated successfully.']);
    }

    /** Update customer address */
    public function updateAddress(Request $request) {
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        Customer_Address::updateOrCreate(
            ['user_id' => $userId],
            $request->only([
                'first_name', 'last_name', 'email', 'mobile', 
                'country_id', 'address', 'apartment', 'city', 'state', 'zip'
            ])
        );

        session()->flash('success', 'Address updated successfully.');
        return response()->json(['status' => true, 'message' => 'Address updated successfully.']);
    }

    /** Show change password page */
    public function changePasswordPage() {
        return view('front.account.change-password');
    }

    /** Change user password */
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            session()->flash('error', 'Your old password is incorrect.');
            return response()->json(['status' => false]);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        session()->flash('success', 'Password changed successfully.');
        return response()->json(['status' => true]);
    }

    /** Show forgot password page */
    public function forgotPassword() {
        return view('front.account.forgot-password');
    }

    /** Handle forgot password */
    public function processForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $user = User::where('email', $request->email)->first();

        Mail::to($request->email)->send(new ResetPasswordEmail([
            'token' => $token,
            'user' => $user,
            'mailSubject' => 'You have requested to reset password'
        ]));

        return redirect()->route('front.forgotPassword')->with('success', 'Please check your inbox to reset your password.');
    }

    /** Show reset password page */
    public function resetPassword($token) {
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$record) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid or expired request.');
        }

        return view('front.account.resetPassword', ['token' => $token]);
    }

    /** Handle reset password form */
    public function processResetPassword(Request $request) {
        $token = $request->token;

        $record = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$record) {
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return redirect()->route('front.resetPassword', $token)->withInput()->withErrors($validator);
        }

        $user = User::where('email', $record->email)->first();
        $user->update(['password' => Hash::make($request->new_password)]);

        DB::table('password_reset_tokens')->where('token', $token)->delete();

        return redirect()->route('front.login')->with('success', 'You have successfully updated your password.');
    }
}
