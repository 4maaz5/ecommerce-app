@extends('front.layouts.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">
                                @if($categories->isNotEmpty())
                                @foreach($categories as $category)
                                <div class="accordion-item">
                                    @if($category->sub_category->isNotEmpty())
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne_{{ $category->id }}" aria-expanded="false" aria-controls="collapseOne">
                                            {{ $category->name }}
                                        </button>
                                    </h2>
                                    @else
                                    <a href="{{ route('front.shop',$category->slug) }}" class="nav-item nav-link {{ ($categorySelected==$category->id)?'text-primary':'' }}">{{ $category->name }}</a>
                                    @endif
                                   @if($category->sub_category->isNotEmpty())
                                    <div id="collapseOne_{{ $category->id }}" class="accordion-collapse collapse {{ ($categorySelected==$category->id)?'show':'' }}" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                        <div class="accordion-body">
                                                <div class="navbar-nav">
                                                    @foreach($category->sub_category as $subCategory)
                                                    <a href="{{ route('front.shop',[$category->slug,$subCategory->slug]) }}" class="nav-item nav-link {{ ($subCategorySelected==$subCategory->id)?'text-primary':'' }}">{{ $subCategory->name }}</a>
                                                    @endforeach

                                                </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Brand</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @if($brands->isNotEmpty())
                                @foreach($brands as $brand)
                                <div class="form-check mb-2">
                                    <input {{ (in_array($brand->id,$brandArray))?'checked':'' }} class="form-check-input brand-label" type="checkbox" name="brand[]" value="{{ $brand->id }}" id="brand_{{ $brand->id }}">
                                    <label class="form-check-label" for="brand_{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                                @endforeach
                            @endif


                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                        <input type="text" class="js-range-slider" name="my_range" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">

                                    <select name="sort" id="sort" class="form-control">
                                        <option value="latest" {{ ($sort=='latest')?'selected':'' }}>Latest</option>
                                        <option value="price_desc" {{ ($sort=='price_desc')?'selected':'' }}>Price High</option>
                                        <option value="price_asc" {{ ($sort=='price_asc')?'selected':'' }}>Price Low</option>
                                    </select>
                                    </div>
                            </div>
                        </div>
                        @if(!empty($products))
                        @foreach($products as $product)
                        <div class="col-md-4">
                            <div class="card product-card">
                                <div class="product-image position-relative">
                                    <a href="{{ route('front.products', $product->slug) }}" class="product-img">
                                        @php
                                            $image=DB::table('product__images')->where('product_id',$product->id)->first();
                                            if (!empty($image)) {
                                                $images=explode('|',$image->image);

                                           foreach ($images as $image) {

                                           }
                                        }
                                        @endphp

                                        <img class="card-img-top" src="{{ URL::to($image) }}" alt="Loading" style="height: 270px">


                                    </a>
                                    <a onclick="addToWishList({{ $product->id }})" class="whishlist" href="javascript:void(0);"><i class="far fa-heart"></i></a>

                                    <div class="product-action">
                                        @if ($product->track_qty == 'Yes')
                                                @if ($product->qty > 0)
                                                    <a class="btn btn-dark" href="javaScript:void(0)"
                                                        onclick="addToCart({{ $product->id }})">
                                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                                    </a>
                                                @else
                                                    <a class="btn btn-dark" href="javascript:void(0)">
                                                        <i class="fa fa-shopping-cart"></i> Out of Stock
                                                    </a>
                                                @endif
                                            @else
                                                <a class="btn btn-dark" href="javaScript:void(0)"
                                                    onclick="addToCart({{ $product->id }})">
                                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                                </a>
                                            @endif
                                    </div>
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="product.php">{{ $product->title }}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>{{ "$".$product->price }}</strong></span>
                                        @if($product->compare_price > 0)
                                        <span class="h6 text-underline"><del>{{ $product->compare_price  }}</del></span>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif





                        <div class="col-md-12 pt-5">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-end">
                                    {{ $products->links() }}

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@section('customJs')
<script type="text/javaScript">
rangeSlider=$(".js-range-slider").ionRangeSlider({
    type:"double",
    min:0,
    max:1000,
    from:{{ ($priceMin) }},
    step:10,
    to:{{ ($priceMax) }},
    skin:"round",
    max_postfix:"+",
    prefix:"$",
    onFinish:function(){
        apply_filters()
    }
});

var slider=$(".js-range-slider").data("ionRangeSlider");

$(".brand-label").change(function(){
apply_filters();
});

$("#sort").change(function(){
    apply_filters();
});

function apply_filters(){
       var brands=[];
    $(".brand-label").each(function(){
        if($(this).is(':checked')==true){
        brands.push($(this).val());
        }
    });

    var url='{{ url()->current() }}?';

     //brand filter
    if (brands.length>0) {
        url+='&brand='+brands.toString();
    }
    //price range filter
    url +='&price_min='+slider.result.from+'&price_max='+slider.result.to;
    //sorting filter

    var keyword=$("#search").val();
    if (keyword.length>0) {
        url+='&search='=keyword;
    }

    url+='&sort='+$("#sort").val();

    window.location.href=url;
}

function addToCart(id) {
    $.ajax({
        url: '{{ route("front.addToCart") }}',
        type: 'POST',
        data: {
            id: id,
            _token: '{{ csrf_token() }}' // Include CSRF token
        },
        dataType: 'json',
        success: function(response) {
            if (response.status==true) {
                window.location.href="{{ route('front.cart') }}";
            }else{
                alert(response.message);
            }
        }
    });
}
function addToWishList(id) {
            $.ajax({
                url: '{{ route('front.wishList') }}',
                type: 'post',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("#modalGrid .modal-body").html(response.message);
                        $("#modalGrid").modal('show');
                    } else {
                        window.location.href = "{{ route('front.login') }}"
                    }
                }
            });

    </script>
@endsection
