<?php

namespace App\Http\Controllers\front;

use App\Models\User;
use App\Models\Order;
use App\Models\Country;
use App\Models\WishList;
use App\Models\Order_item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Customer_Address;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(){
        return view('front.login');
    }
    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
        'email'=>'required|email',
        'password'=>'required'
        ]);
        if ($validator->passes()) {
           if (Auth::attempt(['email'=>$request->email,'password'=>$request->password],$request->get('remembered'))) {
            if (session()->has('url.intended')) {
                // return redirect(session()->get('url.intended'));
                return redirect()->route('account.profile');
            }
            return redirect()->route('account.profile');
           }else{
            session()->flash('error','Either email or password is incorrect');
            return redirect()->route('front.login')
            ->withInput($request->only('email'));
           }
        }else{
            return redirect()->route('front.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }
    public function profile(){

        $user=User::where('id',Auth::user()->id)->first();
        $countries=Country::orderBy('name','ASC')->get();
        $address=Customer_Address::where('user_id',$user->id)->first();
        $data['user']=$user;
        $data['countries']=$countries;
        $data['address']=$address;


return view('front.account.profile',$data);
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('front.login')->with('success','You have been Successfully Loged Out');
    }
    function orders(){
        $user=Auth::user();
        $orders=Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();

     return view('front.account.orders',['orders'=>$orders]);
    }
    function orderDetail($orderId){
        $data=[];
        $user=Auth::user();
        $order=Order::where('user_id',$user->id)->where('id',$orderId)->first();

        $orderItems=Order_item::where('order_id',$orderId)->get();
        $orderItemsCount=Order_item::where('order_id',$orderId)->count();

        $data['order']=$order;
        $data['orderItems']=$orderItems;
        $data['orderItemsCount']=$orderItemsCount;
    return view('front.account.orderDetail',$data);
    }
    public function wishList(){
        $wishLists=WishList::where('user_id',Auth::user()->id)->with('product')->get();
        $data=[];
        $data['wishLists']=$wishLists;
        return view('front.account.wishList',$data);
    }
    public function removeProduct(Request $request){
        $wishList=WishList::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();

        if ($wishList==null) {
            session()->flash('error','Product already removed');
            return response()->json([
                'status'=>true,
            ]);
        }else{

            $wishList=WishList::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
            session()->flash('success','Product removed successfully.');
            return response()->json([
                'status'=>true,
            ]);
        }
    }
    public function updateProfile(Request $request){
        $userId=Auth::user()->id;
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$userId.',id',
            'phone'=>'required'
        ]);
        if ($validator->passes()) {
        $user=User::find($userId);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->phone=$request->phone;
        $user->save();
        session()->flash('success','profile Updated successfully.');
        return response()->json([
         'status'=>true,
         'message'=>'Profile updated Successfully.'
        ]);
        }else{
            return response()->json([
'status'=>false,
'errors'=>$validator->errors()
            ]);
        }
    }

    public function updateAddress(Request $request){
        $userId=Auth::user()->id;
           //validation....
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
        if ($validator->passes()) {


        Customer_Address::updateOrCreate(
            ['user_id' => $userId],
            [
                'user_id' => $userId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country_id,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                // ''=>$request->,
            ]
        );

        session()->flash('success','Address Updated successfully.');
        return response()->json([
         'status'=>true,
         'message'=>'Address updated Successfully.'
        ]);
        }else{
            return response()->json([
'status'=>false,
'errors'=>$validator->errors()
            ]);
        }
    }
    public function changePasswordPage(){
        return view('front.account.change-password');
    }
    public function changePassword(Request $request){
        $validator=Validator::make($request->all(),[
         'old_password'=>'required',
         'new_password'=>'required|min:5',
         'confirm_password'=>'required|same:new_password',
        ]);
        if ($validator->passes()) {
        $user=User::select('id','password')->where('id',Auth::user()->id)->first();
        if (!Hash::check($request->old_password,$user->password)) {
            session()->flash('error','Your old Password is incorrect, Please try again');
            return response()->json([
                'status'=>true,
                ]);
        }
        User::where('id',Auth::user()->id)->update([
            'password'=>Hash::make($request->new_password),
        ]);

        session()->flash('success','You have successfully change your password.');
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
    public function forgotPassword(){
        return view('front.account.forgot password');
    }
    public function processForgotPassword(Request $request){
      $validator=Validator::make($request->all(),[
        'email'=>'required|email|exists:users,email'
      ]);
      if ($validator->fails()) {
        return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
      }
       $token=Str::random(60);
       DB::table('password_reset_tokens')->where('email',$request->email);

       DB::table('password_reset_tokens')->insert([
        'email'=>$request->email,
        'token'=>$token,
        'created_at'=>now()
       ]);
      //send email here
      $user=User::where('email',$request->email)->first();
      $formData=[
       'token'=>$token,
       'user'=>$user,
       'mailSubject'=>'You have requested to reset Password'
      ];

      Mail::to($request->email)->send(new ResetPasswordEmail($formData,$token));
      return redirect()->route('front.forgotPassword')->with('success','Please check your inbox to reset your password.');

    }
    public function resetPassword(){
        $latest=DB::table('password_reset_tokens')->latest('created_at')->first();
        if ($latest->token==null) {
            return redirect()->route('front.forgotPassword')->with('error','Invalid Request');
                    }
       return view('front.account.resetPassword',['token'=>$latest->token]);
    }
    public function processResetPassword(Request $request){
         $token=$request->token;
         $latest=DB::table('password_reset_tokens')->where('token',$token)->first();
         if ($latest->token==null) {
            return redirect()->route('front.forgotPassword')->with('error','Invalid Request');
             }
        $user=User::where('email',$latest->email)->first();
        $validator=Validator::make($request->all(),[
            'new_password'=>'required|min:5',
            'confirm_password'=>'required|same:new_password',
          ]);
          if ($validator->fails()) {
            return redirect()->route('front.resetPassword',$token)->withInput()->withErrors($validator);
          }
          User::where('id',$user->id)->update([
              'password'=>Hash::make($request->new_password)
          ]);
          $latest=DB::table('password_reset_tokens')->latest('created_at')->first();
          $latest=DB::table('password_reset_tokens')->where('token',$latest->token)->delete();
          return redirect()->route('front.login')->with('success','You have successfully updated your password.');
    }
}
