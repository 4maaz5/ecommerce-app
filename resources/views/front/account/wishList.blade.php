@extends('front.layouts.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">My WishList</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-12">
                @if(session()->has('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session()->has('error'))
       <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                </div>
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">WishList</h2>
                        </div>
                        <div class="card-body p-4">
                            @if($wishLists->isNotEmpty())
                            @foreach($wishLists as $wishList)
                            <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom">
                                <div class="d-block d-sm-flex align-items-start text-center text-sm-start">
                                    <a class="d-block flex-shrink-0 mx-auto me-sm-4" href="{{ route('front.products',$wishList->product->slug) }}" style="width: 10rem;">
                                        @php
                                        $image=DB::table('product__images')->where('product_id',$wishList->product_id)->first();
                                        if (!empty($image)) {
                                            $images=explode('|',$image->image);
                                     foreach ($images as $image) {
                                       }
                                    }
                                    @endphp
                                    <img class="card-img-top" src="{{ URL::to($image) }}" alt="Loading" style="height: 200px">

                                    </a>
                                    <div class="pt-2">
                                        <h3 class="product-title fs-base mb-2"><a href="{{ route('front.products',$wishList->product->slug) }}">{{ $wishList->product->title }}</a></h3>
                                        <div class="fs-lg text-accent pt-2">
                                            <span class="h5"><strong>{{ "$".$wishList->product->price }}</strong></span>
                                            @if($wishList->product->compare_price > 0)
                                            <span class="h6 text-underline"><del>{{ $wishList->product->compare_price  }}</del></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2 ps-sm-3 mx-auto mx-sm-0 text-center">
                                    <button onclick="removeProduct({{ $wishList->product_id }});" class="btn btn-outline-danger btn-sm" type="button"><i class="fas fa-trash-alt me-2"></i>Remove</button>
                                </div>
                            </div>
                            @endforeach
@else
<div>
<h5>Your wishList is empty!!</h5>
</div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
@section('customJs')
<script>
    function removeProduct(id){
        $.ajax({
          url:'{{ route("account.removeProduct") }}',
          type:'post',
          data:{id:id},
          dataType:'json',
          success: function(response){
           if (response.status==true) {
            window.location.href="{{ route('account.wishList') }}"
           }
          }
        });
    }

    </script>
@endsection
