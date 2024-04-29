<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    public function index(Request $request){
        $pages=Pages::orderBy('id','ASC');
        if(!empty($request->get('keyword'))){
            $pages=$pages->where('name','like','%'.$request->get('keyword').'%');
          }
          $pages=$pages->paginate(8);
        $data['pages']=$pages;
        return view('admin.pages.list',$data);
    }
    public function create(){
       return view('admin.pages.create');
    }
    public function store(Request $request){
       $validator=Validator::make($request->all(),[
        'name'=>'required',
        'slug'=>'required|unique:pages',
       ]);
       if ($validator->passes()) {
         $user=new Pages();
         $user->name=$request->name;
         $user->slug=$request->slug;
         $user->content=$request->content;
         $user->save();
         session()->flash('success','New Page added successfully.');
         return response()->json([
          'status'=>true,
          'message'=>'New Page added successfully.'
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
            $page=Pages::find($id);
            return view('admin.pages.edit',['page'=>$page]);
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
            'slug'=>'required|unique:pages',
           ]);
           if ($validator->passes()) {
             $user=Pages::find($id);
             $user->name=$request->name;
             $user->slug=$request->slug;
             $user->content=$request->content;
             $user->save();
             session()->flash('success','Page updated successfully.');
             return response()->json([
              'status'=>true,
              'message'=>'Page updated successfully.'
             ]);
           }else{
            return response()->json([
              'status'=>false,
              'errors'=>$validator->errors()
            ]);
           }
    }
    public function destroy($pageId,Request $request){
        $page=Pages::find($pageId);
        if(empty($page)){
           $request->session()->flash('error','page not Found');
           return response()->json([
           'status'=>true,
           'message'=>'page not Found'
           ]);
        }
        $page->delete();
        $request->session()->flash('danger','page Deleted Successfully');
        return response()->json([
           'status'=>true,
           'message'=>'page Deleted Successfully'
        ]);
   }
}
