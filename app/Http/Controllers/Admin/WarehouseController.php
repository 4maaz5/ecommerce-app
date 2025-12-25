<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function list()
    {
        $warehouses = Warehouse::all();

        return view('admin.Warehouse.list', compact('warehouses'));
    }

    public function index()
    {
        return view('admin.Warehouse.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:warehouses,code',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $warehouse = new Warehouse;

        $warehouse->name = $request->name;
        $warehouse->code = $request->code;
        $warehouse->city = $request->city;
        $warehouse->address = $request->address;
        $warehouse->capacity = $request->capacity;
        $warehouse->description = $request->description;

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('warehouses', 'public');
            $warehouse->image_path = 'storage/'.$path; // Save final path
        }

        $warehouse->save();

        return redirect()->route('warehouse.list')->with('success', 'Warehouse created successfully!');
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrfail($id);

        return view('admin.Warehouse.edit', compact('warehouse'));
    }

    public function update(Request $request, $id)
    {
        // Find the warehouse
        $warehouse = Warehouse::findOrFail($id);

        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:warehouses,code,'.$warehouse->id,
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Update warehouse fields
        $warehouse->name = $request->name;
        $warehouse->code = $request->code ?? $warehouse->code;
        $warehouse->city = $request->city;
        $warehouse->address = $request->address;
        $warehouse->capacity = $request->capacity;
        // $warehouse->manager_id = $request->manager_id;
        $warehouse->description = $request->description;

        // Handle image upload (if new image is provided)
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($warehouse->image_path && Storage::disk('public')->exists($warehouse->image_path)) {
                Storage::disk('public')->delete($warehouse->image_path);
            }

            $path = $request->file('image')->store('warehouses', 'public');
            $warehouse->image_path = 'storage/'.$path;
        } elseif ($request->image_id) {
            // If using Dropzone with temporary upload
            $warehouse->image_path = $request->image_id;
        }

        // Save changes
        $warehouse->save();

        return redirect()->route('warehouse.list')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();
            session()->flash('message', 'Warehouse Deleted Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Warehouse deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
