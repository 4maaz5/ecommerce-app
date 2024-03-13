<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Product_Image;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request){
        $products=Product::latest('id')->with('product_images');
        if($request->get('keyword')!=""){
            $products=$products->where('title','like','%'.$request->keyword.'%');
        }
        $products=$products->paginate(8);
        $data['products']=$products;
        return view('admin.products.list',$data);
    }
    public function create(){
        $data=[];
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$brands;
        return view('admin.products.create',$data);
    }

    public function store(Request $request){
    $request->validate([
            'title'=>'required',
            'slug'=>'required|unique:products',
            'price'=>'required|numeric',
            'sku'=>'required|unique:products',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ]);

         $product=new Product();
         $product->title=$request->title;
         $product->slug=$request->slug;
         $product->description=$request->description;
         $product->price=$request->price;
         $product->compare_price=$request->compare_price;
         $product->sku=$request->sku;
         $product->barcode=$request->barcode;
         $product->track_qty=$request->track_qty;
         $product->qty=$request->qty;
         $product->status=$request->status;
         $product->category_id=$request->category;
         $product->sub_category_id=$request->sub_category;
         $product->brand_id=$request->brand;
         $product->is_featured=$request->is_featured;
         $product->short_description=$request->short_description;
         $product->shiping_returns=$request->shiping_returns;
         $product->related_products=(!empty($request->related_products))?implode(',',$request->related_products):'';
         $product->save();


          //images code
          $image=array();
          if ($files=$request->file('image')) {
             foreach ($files as $file) {
                 $image_name=md5(rand(1000,10000));
                 $ext=strToLower($file->getClientOriginalExtension());
                 $image_full_name=$image_name.'.'.$ext;
                 $upload_path='public/temp/thumb/';
                 $image_url=$upload_path.$image_full_name;
                 $file->move($upload_path,$image_full_name);
                 $image[]=$image_url;
             }
          }
          $product_images=new Product_Image();
          $product_images->product_id=$product->id;
          $product_images->image=implode('|',$image);
          $product_images->save();

         $request->session()->flash('success','Product Added Succesfully');
         return redirect()->route('products.index');
        }

  public function edit($id){
    $product=Product::find($id);
    $categories=Category::where('id',$product->category_id)->get();
    $subCategories=SubCategory::where('category_id',$product->category_id)->get();
    $brands=Brand::all();
    //related products
    $relatedProducts=[];

    if ($product->related_products!='') {
        $productArray=explode(',',$product->related_products);
        $relatedProducts=Product::whereIn('id',$productArray)->get();
    }
    // dd($relatedProducts);

    $data=[];
    $data['categories']=$categories;
    $data['brands']=$brands;
    $data['product']=$product;
    $data['subCategories']=$subCategories;
    $data['relatedProducts']=$relatedProducts;
    return view('admin.products.edit',$data);
  }
  public function update(Request $request, $id){
    // dd("test");
    $rules=[
        'title'=>'required',
        'slug'=>'required|unique:products',
        'price'=>'required|numeric',
        'sku'=>'required',
        'track_qty'=>'required|in:Yes,No',
        'category'=>'required|numeric',
        'is_featured'=>'required|in:Yes,No',
    ];
    // dd("test2");
    if (!empty($request->track_qty) && $request->track_qty=="Yes") {
        $rules['qty']='required|numeric';
    }
    // dd('test3');
    $validator=Validator::make($request->all(),$rules);
    // if ($validator->passes()) {
        // dd("check4");
     $product=Product::find($id);
     $product->title=$request->title;
     $product->slug=$request->slug;
     $product->description=$request->description;
     $product->price=$request->price;
     $product->compare_price=$request->compare_price;
     $product->sku=$request->sku;
     $product->barcode=$request->barcode;
     $product->track_qty=$request->track_qty;
     $product->qty=$request->qty;
     $product->status=$request->status;
     $product->category_id=$request->category;
     $product->sub_category_id=$request->sub_category;
     $product->brand_id=$request->brand;
     $product->is_featured=$request->is_featured;
     $product->short_description=$request->short_description;
     $product->shiping_returns=$request->shiping_returns;
     $product->related_products=(!empty($request->related_products))?implode(',',$request->related_products):'';
     $product->save();
         //images code
         $image=array();
         if ($files=$request->file('image')) {
            foreach ($files as $file) {
                $image_name=md5(rand(1000,10000));
                $ext=strToLower($file->getClientOriginalExtension());
                $image_full_name=$image_name.'.'.$ext;
                $upload_path='public/temp/thumb/';
                $image_url=$upload_path.$image_full_name;
                $file->move($upload_path,$image_full_name);
                $image[]=$image_url;
            }
         }
         $id=Product_image::where('product_id',$id)->first();
         $idd=$id->id;
         $product_images=Product_Image::find($idd);
         $product_images->product_id=$product->id;
         $product_images->image=implode('|',$image);
         $product_images->save();
     $request->session()->flash('success','Product Updated Succesfully');
     return redirect()->route('products.index');
//     }
//     else{
//     return redirect()->back()->with('error','Something went wrong!');
// }
  }
  public function destroy($id){
    $productDelete=Product::find($id);
    $productImages=Product_Image::where('product_id',$id);
    $productDelete->delete();
    $productImages->delete();
    return redirect()->back()->with('danger','Product deleted Successfully!');
  }
  public function getProducts(Request $request){
    $tempProduct=[];
     if ($request->term!="") {
        $products=Product::where('title','like','%'.$request->term)->get();

        if ($products!=null) {
            foreach ($products as $product) {
                $tempProduct[]=array('id'=>$product->id,'text'=>$product->title);
            }
        }
     }
     return response()->json([
        'tags'=>$tempProduct,
        'status'=>true
     ]);
  }
}
