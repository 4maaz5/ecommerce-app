<?php

namespace App\Http\Controllers\front;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Rating;
use App\Models\SubCategory;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        $subCategorySelected = '';
        $brandArray = [];

        $categories = Category::orderBy('name', 'ASC')
            ->with('sub_category')
            ->where('status', '1')
            ->get();

        $brands = Brand::orderBy('name', 'ASC')
            ->where('status', '1')
            ->get();
        $products = Product::orderBy('id', 'DESC')
        ->where('status', '1');
// dd($categorySlug);

        // Apply filters here
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            $products = $products->where('category_id', $category->id);
            $categorySelected = $category->id;
        }
        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            $products = $products->where('sub_category_id', $subCategory->id);
            $subCategorySelected = $subCategory->id;
        }


        if (!empty($request->get('brand'))) {
            $brandArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandArray);
        }


        if ($request->get('price_max') != '' && $request->get('price_min') != '') {
            if ($request->get('price_max' == 1000)) {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
            } else {
                $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
            }
        }

        if (!empty($request->get('search'))) {
            $products = $products->where('title','like','%'. $request->get('search').'%');

        }

        // //  dd($request->get('sort') );
        if ($request->get('sort')!='') {

            if ($request->get('sort') == 'latest') {
                $products = $products->orderBy('id', 'DESC');
                // dd('lat');
            }
             elseif ($request->get('sort') == 'price_asc') {
                $products = $products->orderBy('price', 'ASC');
                // dd('pr1');
            } else {
                $products = $products->orderBy('price', 'DESC');
                // dd('pr2');
            }
        }
        else {
            $products = $products->orderBy('id', 'DESC');
        }

        $products = $products->paginate(6);
        // dd($products);
        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['brands'] = $brands;
        $data['categorySelected'] = $categorySelected;
        $data['subCategorySelected'] = $subCategorySelected;
        $data['brandArray'] = $brandArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = (intval($request->get('price_min')));
        $data['sort'] = $request->get('sort');
        return view('front.shop', $data);
    }
    public function saveRating(Request $request,$product_id){
        $validator=Validator::make($request->all(),[
          'name'=>'required|min:5',
          'email'=>'required|email',
          'comment'=>'required|min:10',
          'rating'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
            'status'=>false,
            'error'=>$validator->errors()
            ]);
        }
        $check=Rating::where('email',$request->email)->first();
        if ($check!=null) {
            session()->flash('error','You already rated this product.');
            return response()->json([
              'status'=>true,
              'message'=>'You already rated this product.'
            ]);
        }else{
            $rating=new Rating();
            $rating->product_id=$product_id;
            $rating->user_name=$request->name;
            $rating->email=$request->email;
            $rating->comment=$request->comment;
            $rating->rating=$request->rating;
            $rating->status=0;
            $rating->save();
            session()->flash('success','Thanks for your rating.');
            return response()->json([
              'status'=>true,
              'message'=>'Thanks for your rating.'
            ]);

        }

    }
}
