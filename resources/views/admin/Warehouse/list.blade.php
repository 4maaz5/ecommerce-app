@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Warehouses</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('warehouse.create') }}" class="btn btn-primary">New Warehouse</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('message'))
                <div class="alert alert-danger">{{ session('message') }}</div>
            @endif
            <div class="card">
                <form action="" method="get">
                    <div class="card-header">
                        <div class="card-tools">
                            <div class="input-group input-group" style="width: 250px;">
                                <input type="text" value="{{ Request::get('keyword') }}" name="keyword"
                                    class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Capacity</th>
                            <th>Manager</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($warehouses->isNotEmpty())
                            @foreach ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->city ?? '-' }}</td>
                                    <td>{{ $warehouse->capacity ?? '-' }}</td>
                                    <td>{{ $warehouse->manager?->name ?? '-' }}</td>
                                    <td>

                                    </td>
                                    <td>
                                        <!-- Edit -->
                                        <a href="{{ route('warehouse.edit', $warehouse->id) }}">
                                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                </path>
                                            </svg>
                                        </a>

                                        <!-- Delete -->
                                        <a href="#" onclick="deleteWarehouse({{ $warehouse->id }})"
                                            class="text-danger w-4 h-4 mr-1">
                                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No Warehouses Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>
            <div class="card-footer clearfix">
                {{-- {{ $warehouses->links() }} --}}

            </div>
        </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
    </div>
@endsection
@section('customjs')
    <script>
        function deleteWarehouse(id) {
            // Use Blade's route helper with placeholder
            var url = '{{ route('warehouse.delete', ':id') }}';
            url = url.replace(':id', id); // Replace placeholder with actual ID

            if (confirm("Are you sure you want to delete?")) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            // Reload page or redirect
                            window.location.href = "{{ route('warehouse.list') }}";
                        } else {
                            alert(response.message || 'Failed to delete');
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>
@endsection
