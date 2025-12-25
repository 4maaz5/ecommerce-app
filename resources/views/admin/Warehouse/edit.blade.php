@extends('admin.layout.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Warehouse</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('warehouse.list') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="{{ isset($warehouse) ? route('warehouse.update', $warehouse->id) : route('warehouse.store') }}"
                method="POST" id="WarehouseForm" enctype="multipart/form-data">
                @csrf
                @if (isset($warehouse))
                    @method('PUT')
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <!-- Warehouse Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Warehouse Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="e.g. Riyadh Central Warehouse"
                                        value="{{ old('name', $warehouse->name ?? '') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Code -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code">Code</label>
                                    <input type="text" readonly name="code" id="code" class="form-control"
                                        placeholder="Auto generated" value="{{ old('code', $warehouse->code ?? '') }}">
                                    @error('code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- City -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city">City</label>
                                    <input type="text" name="city" id="city" class="form-control"
                                        placeholder="Riyadh, Jeddah, Dammam..."
                                        value="{{ old('city', $warehouse->city ?? '') }}">
                                    @error('city')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" id="address" class="form-control"
                                        placeholder="Full warehouse location"
                                        value="{{ old('address', $warehouse->address ?? '') }}">
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Capacity -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacity">Capacity (in pallets/CBM)</label>
                                    <input type="number" name="capacity" id="capacity" class="form-control"
                                        placeholder="e.g. 5000" value="{{ old('capacity', $warehouse->capacity ?? '') }}">
                                    @error('capacity')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Manager Assign -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="manager_id">Warehouse Manager</label>
                                    <select name="manager_id" id="manager_id" class="form-control">
                                        <option value="">Select Manager</option>
                                        {{-- @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}"
                                                {{ old('manager_id', $warehouse->manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                    @error('manager_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" id="image_id" name="image_id"
                                        value="{{ old('image_id', $warehouse->image_path ?? '') }}">
                                    <label for="image">Warehouse Image</label>
                                    <div id="image" class="dropzone dz-clickable">
                                        <div class="dz-message needsclick">
                                            @if (isset($warehouse) && $warehouse->image_path)
                                                <img src="{{ asset($warehouse->image_path) }}" alt="Warehouse Image"
                                                    width="100"><br>
                                            @endif
                                            <br><input type="file" name="image"><br><br>
                                        </div>
                                    </div>
                                    @error('image')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description (optional)</label>
                                    <textarea name="description" id="description" class="form-control" rows="3"
                                        placeholder="Short notes about warehouse">{{ old('description', $warehouse->description ?? '') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($warehouse) ? 'Update Warehouse' : 'Create Warehouse' }}
                    </button>
                    <a href="{{ route('warehouse.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

            </form>



        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customjs')
    <script>
        document.getElementById("name").addEventListener("keyup", function() {
            let code = this.value.toLowerCase()
                .replace(/ /g, "-")
                .replace(/[^a-z0-9\-]/g, "");
            document.getElementById("code").value = code;
        });
    </script>
@endsection
