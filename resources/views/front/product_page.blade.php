@extends('front.layouts.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col-md-5">

                        @php
                        $image=DB::table('product__images')->where('product_id',$product->id)->first();
                        if (!empty($image)) {
                            $images=explode('|',$image->image);
                        $count=0;
                       foreach ($images as $image) {
                        $count++;
                        if ($count==1) {
                            @endphp
                        <img src="{{ URL::to($image) }}" alt="Image" style="width: 450px; margin-left:50px;height:350px;">
                        @php
                        }
                        else {
                            @endphp
                        <img src="{{ URL::to($image) }}" alt="Image" style="width: 160px;margin-left:10px;height:130px;margin-top:10px;">
                        @php
                        }
                        @endphp
                        @php

                       }
                    }
                   @endphp


                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            <div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div>
                            <small class="pt-1">(99 Reviews)</small>
                        </div>
                        <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                        <h2 class="price ">${{ $product->price }}</h2>

                        <p>{!! $product->short_description !!}</p>
                        <a href="javascript:void(0);" onclick="addToCart({{ $product->id }});" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> &nbsp;ADD TO CART</a>
                    </div>
                </div>

                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping & Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <p>
{!! $product->description !!}                                </p>
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                {!! $product->shiping_returns !!}
                            </div>
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                ...........
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if(!empty($relatedProducts))
    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Related Products</h2>
            </div>
            <div class="col-md-12">
                <div id="related-products" class="carousel">

                        @foreach($relatedProducts as $relproduct)
                    <div class="card product-card">
                        <div class="product-image position-relative">
                            @php
                            $image=DB::table('product__images')->where('product_id',$relproduct->id)->first();
                            if (!empty($image)) {
                                $images=explode('|',$image->image);

                           foreach ($images as $image) {

                           }
                        }
                        @endphp
                        <img class="card-img-top" src="{{ URL::to($image) }}" alt="Loading" style="height: 270px">
                            <a class="whishlist" href="222"><i class="far fa-heart"></i></a>

                            <div class="product-action">
                                <a class="btn btn-dark" href="{{ route('front.products',$relproduct->slug) }}">
                                    <i class="fa fa-shopping-cart"></i> Add To Cart
                                </a>
                            </div>
                        </div>
                        <div class="card-body text-center mt-3">
                            <a class="h6 link" href="">{{ $relproduct->title }}</a>
                            <div class="price mt-2">
                                <span class="h5"><strong>${{ $relproduct->price }}</strong></span>
                                @if($relproduct->compare_price>0)
                                <span class="h6 text-underline"><del>${{ $relproduct->compare_price }}</del></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
</main>
@endsection
@section('customJs')
<script type="text/javascript">
function addToCart(id){
    $.ajax({
url:'{{ route("front.addToCart") }}',
Type:'post',
data:{id:id},
dataType:'json',
success:function(response){

}
    });
}
    </script>
@endsection
