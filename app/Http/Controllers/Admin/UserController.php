<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a paginated list of users with optional search by keyword.
     */
    public function index(Request $request)
    {
        // Start query to get users ordered by ID ascending
        $users = User::orderBy('id', 'ASC');

        // Filter users by name if a keyword is provided
        if (!empty($request->get('keyword'))) {
            $users = $users->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // Paginate the results
        $users = $users->paginate(8);

        // Pass the users to the view
        $data['users'] = $users;
        return view('admin.users.list', $data);
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in the database.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            // Create and save new user
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->password = $request->password;
            $user->save();

            // Flash success message and return JSON response
            session()->flash('success', 'New User added successfully.');
            return response()->json([
                'status' => true,
                'message' => 'New User added successfully.'
            ]);
        } else {
            // Return validation errors as JSON
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing an existing user.
     */
    public function edit($id)
    {
        if (!empty($id)) {
            $user = User::find($id);
            return view('admin.users.edit', ['user' => $user]);
        } else {
            // Return error if ID is not provided
            session()->flash('success', 'ID not Found');
            return response()->json([
                'status' => false,
                'message' => 'ID not Found'
            ]);
        }
    }

    /**
     * Update an existing user's details.
     */
    public function update(Request $request, $id)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required'
        ]);

        if ($validator->passes()) {
            // Find and update user
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->password = $request->password;
            $user->save();

            // Flash success message and return JSON response
            session()->flash('success', 'User updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully.'
            ]);
        } else {
            // Return validation errors as JSON
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Delete an existing user.
     */
    public function destroy($userId, Request $request)
    {
        // Find user by ID
        $user = User::find($userId);

        if (empty($user)) {
            // Flash error message if user not found
            $request->session()->flash('error', 'User not Found');
            return response()->json([
                'status' => true,
                'message' => 'User not Found'
            ]);
        }

        // Delete user and flash success message
        $user->delete();
        $request->session()->flash('danger', 'User Deleted Successfully');
        return response()->json([
            'status' => true,
            'message' => 'User Deleted Successfully'
        ]);
    }
}
