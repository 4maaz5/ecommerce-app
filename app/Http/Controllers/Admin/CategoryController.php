<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories=Category::latest();
        if(!empty($request->get('keyword'))){
          $categories=$categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories=$categories->paginate(10);
        return view('admin.Category.list',compact('categories'));
    }
    public function create(){
        return view('admin.Category.create');
    }
    public function store(Request $request){
    $validator=Validator::make($request->all(),[
        'name'=>'required',
        'slug'=>'required|unique:categories',
    ]);
    if ($validator->passes()) {
       $category=new Category();
       $category->name=$request->name;
       $category->slug=$request->slug;
       $category->status=$request->status;
       $category->save();

       // storing image
       if(!empty($request->image_id)){
        $tempImage=TempImage::find($request->image_id);
        $extArray=explode('.',$tempImage->name);
        $ext=last($extArray);
        $newImageName=$category->id.'.'.$ext;
        $spath=public_path().'/temp/'.$tempImage->name;
        $dpath=public_path().'/uploads/category/'.$newImageName;
        File::copy($spath,$dpath);
        // //generate image thumbnail
        // $dpath=public_path().'/uploads/category/thumb/'.$newImageName;
        // $img=Image::make($spath);
        // $img->resize(450,600);
        // $img->save($dpath);

        $category->image=$newImageName;
       $category->save();
       }

       $request->session()->flash('success','Category Added Successfully!');
       return response()->json([
        'status'=>true,
        'message'=>'Category Added Successfully!'
       ]);
    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }
    }
    public function edit($categoryId){
        $category=Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.view');
        }
        return view('admin.Category.edit',compact('category'));
    }
    public function update($categoryId, Request $request){
        $category=Category::find($categoryId);
        if (empty($category)) {
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'Category not Found!'
            ]);
        }
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:categories,slug,'.$category->id.',id',
        ]);
        if ($validator->passes()) {
           $category->name=$request->name;
           $category->slug=$request->slug;
           $category->status=$request->status;
           $category->save();


           $oldImage=$category->image;

           // storing image
           if(!empty($request->image_id)){
            $tempImage=TempImage::find($request->image_id);
            $extArray=explode('.',$tempImage->name);
            $ext=last($extArray);
            $newImageName=$category->id.'-'.time().'.'.$ext;
            $spath=public_path().'/temp/'.$tempImage->name;
            $dpath=public_path().'/uploads/category/'.$newImageName;
            File::copy($spath,$dpath);
            // //generate image thumbnail
            // $dpath=public_path().'/uploads/category/thumb/'.$newImageName;
            // $img=Image::make($spath);
            // $img->resize(450,600);
            // $img->save($dpath);

            $category->image=$newImageName;
           $category->save();
           //delete old image
           File::delete(public_path().'/uploads/category/'.$oldImage);
           }

           $request->session()->flash('success','Category Updated Successfully!');
           return response()->json([
            'status'=>true,
            'message'=>'Category updated Successfully!'
           ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($categoryId,Request $request){
         $category=Category::find($categoryId);
         if(empty($category)){
            $request->session()->flash('error','Category not Found');
            return response()->json([
            'status'=>true,
            'message'=>'Category not Found'
            ]);
         }
         File::delete(public_path().'/uploads/category/'.$category->image);
         $category->delete();
         $request->session()->flash('success','Category Deleted Successfully');
         return response()->json([
            'status'=>true,
            'message'=>'Category Deleted Successfully'
         ]);
    }
}
