<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands=Brand::orderBy('id');
        if($request->get('keyword')){
            $brands=$brands->where('name','like','%'.$request->get('keyword').'%');
          }
        $brands=$brands->paginate(5);
        return view('admin.brands.list',compact('brands'));
    }
    public function create(){
        return view('admin.brands.create');
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required'
        ]);
        if($validator->passes()){
            $brands=new  Brand();
            $brands->name=$request->name;
            $brands->slug=$request->slug;
            $brands->status=$request->status;
            $brands->save();
            return response([
                'status'=>true,
                'message'=>'Brand added Successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit($id){
        $brand=Brand::find($id);
        return view('admin.brands.edit',compact('brand'));
    }
    public function update($id,Request $request){
        $brands=Brand::find($id);
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required'
        ]);
        if($validator->passes()){
            $brands->name=$request->name;
            $brands->slug=$request->slug;
            $brands->status=$request->status;
            $brands->save();
            return response([
                'status'=>true,
                'message'=>'Brand updated Successfully'
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($id,Request $request){
        $brand=Brand::find($id);
        if(empty($brand)){
           $request->session()->flash('error','Brand not Found');
           return response()->json([
           'status'=>true,
           'message'=>'Brand not Found'
           ]);
        }
        $brand->delete();
        $request->session()->flash('success','Brand Deleted Successfully');
        return response()->json([
           'status'=>true,
           'message'=>'Brand Deleted Successfully'
        ]);
   }
    }

