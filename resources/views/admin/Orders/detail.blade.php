@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{ $order->id }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header pt-3">
                            <div class="row invoice-info">
                                <div class="col-sm-4 invoice-col">
                                    <h1 class="h5 mb-3">Shipping Address</h1>
                                    <address>
                                        <strong>{{ $order->first_name . ' ' . $order->last_name }}</strong><br>
                                        {{ $order->address }}<br>
                                        {{ $order->city }}, {{ $order->zip }} {{ $order->countryName }}<br>
                                        Phone: {{ $order->mobile }}<br>
                                        Email: {{ $order->email }}
                                    </address>
                                    <strong>Shipped Date</strong><br>
                                    @if (!empty($order->shipped_date))
                                        {{ $order->shipped_date }}
                                    @else
                                        n/a
                                    @endif
                                </div>

                                <div class="col-sm-4 invoice-col">
                                    <b>Invoice #007612</b><br>
                                    <br>
                                    <b>Order ID:</b> {{ $order->id }}<br>
                                    <b>Total:</b> ${{ number_format($order->grand_total, 2) }}<br>

                                    @if ($order->status == 'pending')
                                        <span class="text-danger">Pending</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="text-info">Shipped</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="text-success">Delivered</span>
                                    @else
                                        <span class="text-danger">Cancelled</span>
                                    @endif

                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th width="100">Price</th>
                                        <th width="100">Qty</th>
                                        <th width="100">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItem as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>${{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach


                                    <tr>
                                        <th colspan="3" class="text-right">Subtotal:</th>
                                        <td>${{ number_format($order->sub_Total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">
                                            Discount:{{ !empty($order->coupon_code) ? '(' . $order->coupon_code . ')' : '' }}
                                        </th>
                                        <td>${{ number_format($order->discount, 2) }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="3" class="text-right">Shipping:</th>
                                        <td>${{ number_format($order->shipping, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-right">Grand Total:</th>
                                        <td>${{ number_format($order->grand_total - $order->discount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">

                    <div class="card">
                        <form action="{{ route('order.changeStatus', $order->id) }}" method="post"
                            name="changeOrderStatusForm" id="changeOrderStatusForm">
                            @csrf
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                        </option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Shipped Date</label>
                                    <input placeholder="Shipped Date" value="{{ $order->shipped_date }}" type="date"
                                        name="shipped_date" id="shipped_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                    </div>
                    </form>
                    <div class="card">
                        <form action="{{ route('order.sendInvoiceEmail',$order->id) }}" method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                            @csrf
                            <div class="card-body">
                                <h2 class="h4 mb-3">Send Inovice Email</h2>
                                <div class="mb-3">
                                    <select name="userType" id="userType" class="form-control">
                                        <option value="customer">Customer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
    </div>
@endsection
@section('customJs')
    <script>
        $("#changeOrderStatusForm").submit(function(event) {
            event.preventDefault(); // Prevents the default form submission behavior

            if (confirm("Are you sure you want to change Status?")) {

            $.ajax({
                url: '{{ route('order.changeStatus', $order->id) }}', // This line seems to be using Blade templating syntax, which is typically used in Laravel
                type: 'post',
                data: $(this).serializeArray(), // Serializes the form data
                dataType: 'json',
                success: function(response) { // Executes if the AJAX request is successful
                    window.location.href =
                    '{{ route('orders.detail', $order->id) }}'; // Redirects to a specific route
                }
            });
        }
        });

        $('#sendInvoiceEmail').submit(function(event) {
            event.preventDefault(); // Prevents the default form submission behavior
            var element = $(this);



            $.ajax({
                url: '{{ route('order.sendInvoiceEmail', $order->id) }}', // This line seems to be using Blade templating syntax, which is typically used in Laravel
                type: 'post',
                data: element.serializeArray(), // Serializes the form data
                dataType: 'json',
                success: function(response) { // Executes if the AJAX request is successful
                    window.location.href =
                    '{{ route('orders.detail', $order->id) }}'; // Redirects to a specific route
                }
            });
        });
    </script>
@endsection
