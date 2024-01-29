<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subCategories=SubCategory::select('sub_categories.*','categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword'))){
          $subCategories=$subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
          $subCategories=$subCategories->orwhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subCategories=$subCategories->paginate(10);
        return view('admin.sub_Category.list',compact('subCategories'));
    }
    public function create(){
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.sub_category.create',$data);
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required'
        ]);
        if ($validator->passes()) {
            $subCategory=new SubCategory();
            $subCategory->name=$request->name;
            $subCategory->slug=$request->slug;
            $subCategory->status=$request->status;
            $subCategory->category_id=$request->category;
            $subCategory->save();
            $request->session()->flash('success','Sub Category created Successfully.');
            return response([
             'status'=>true,
            'message'=>'Sub Category created Successfully.'
            ]);
        }
        else{
            return response([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit($subCategoryId){
        $subCategory=SubCategory::find($subCategoryId);
        if ($subCategory) {
            $categories=Category::all();
        return view('admin.sub_category.edit',compact('subCategory','categories'));
        }
        else{
            return redirect()->back();
        }
    }
    public function update($id, Request $request){
        $subCategory=SubCategory::find($id);
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories,slug,'.$subCategory->id.'id',
            'category'=>'required',
            'status'=>'required'
        ]);
        if ($validator->passes()) {
            $subCategory->name=$request->name;
            $subCategory->slug=$request->slug;
            $subCategory->status=$request->status;
            $subCategory->category_id=$request->category;
            $subCategory->save();
            $request->session()->flash('success','Sub Category updated Successfully.');
            return response([
             'status'=>true,
            'message'=>'Sub Category updated Successfully.'
            ]);
        }
        else{
            return response([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($categoryId,Request $request){
        $category=SubCategory::find($categoryId);
        if(empty($category)){
           $request->session()->flash('error','Category not Found');
           return response()->json([
           'status'=>true,
           'message'=>'Category not Found'
           ]);
        }
        // File::delete(public_path().'/uploads/category/'.$category->image);
        $category->delete();
        $request->session()->flash('success','Sub Category Deleted Successfully');
        return response()->json([
           'status'=>true,
           'message'=>'Sub php artisan make:migration Category Deleted Successfully'
        ]);
   }
}
