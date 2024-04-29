<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request){
        $users=User::orderBy('id','ASC');
        if(!empty($request->get('keyword'))){
            $users=$users->where('name','like','%'.$request->get('keyword').'%');
          }
          $users=$users->paginate(8);
        $data['users']=$users;
        return view('admin.users.list',$data);
    }
    public function create(){
       return view('admin.users.create');
    }
    public function store(Request $request){
       $validator=Validator::make($request->all(),[
        'name'=>'required',
        'email'=>'required|email|unique:users,email',
        'password'=>'required'
       ]);
       if ($validator->passes()) {
         $user=new User();
         $user->name=$request->name;
         $user->email=$request->email;
         $user->phone=$request->phone;
         $user->status=$request->status;
         $user->password=$request->password;
         $user->save();
         session()->flash('success','New User added successfully.');
         return response()->json([
          'status'=>true,
          'message'=>'New User added successfully.'
         ]);
       }else{
        return response()->json([
          'status'=>false,
          'errors'=>$validator->errors()
        ]);
       }
    }
    public function edit($id){
        if (!empty($id)) {
            $user=User::find($id);
            return view('admin.users.edit',['user'=>$user]);
        }else{
            session()->flash('success','ID not Fount');
            return response()->json([
            'status'=>false,
            'message'=>'ID not Found'
            ]);
        }
    }
    public function update(Request $request,$id){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'password'=>'required'

           ]);
           if ($validator->passes()) {
             $user=User::find($id);
             $user->name=$request->name;
             $user->email=$request->email;
             $user->phone=$request->phone;
             $user->status=$request->status;
             $user->password=$request->password;
             $user->save();
             session()->flash('success','User updated successfully.');
             return response()->json([
              'status'=>true,
              'message'=>'User updated successfully.'
             ]);
           }else{
            return response()->json([
              'status'=>false,
              'errors'=>$validator->errors()
            ]);
           }
    }
    public function destroy($userId,Request $request){
        $user=User::find($userId);
        if(empty($user)){
           $request->session()->flash('error','User not Found');
           return response()->json([
           'status'=>true,
           'message'=>'User not Found'
           ]);
        }
        $user->delete();
        $request->session()->flash('danger','User Deleted Successfully');
        return response()->json([
           'status'=>true,
           'message'=>'User Deleted Successfully'
        ]);
   }
}
