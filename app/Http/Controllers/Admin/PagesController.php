<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    /**
     * Display a paginated list of all pages.
     * Supports keyword search on page name.
     */
    public function index(Request $request)
    {
        // Get pages ordered by ID ascending
        $pages = Pages::orderBy('id','ASC');

        // Apply search filter if keyword is provided
        if(!empty($request->get('keyword'))) {
            $pages = $pages->where('name','like','%'.$request->get('keyword').'%');
        }

        // Paginate results
        $pages = $pages->paginate(8);

        // Pass pages to view
        $data['pages'] = $pages;
        return view('admin.pages.list', $data);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page in storage.
     * Validates required fields: name and unique slug.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:pages',
        ]);

        if ($validator->passes()) {
            // Create new page
            $user = new Pages();
            $user->name = $request->name;
            $user->slug = $request->slug;
            $user->content = $request->content;
            $user->save();

            // Flash success message
            session()->flash('success','New Page added successfully.');

            return response()->json([
                'status'=>true,
                'message'=>'New Page added successfully.'
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    /**
     * Show the form for editing an existing page by ID.
     */
    public function edit($id)
    {
        if (!empty($id)) {
            // Find the page
            $page = Pages::find($id);

            // Return edit view with page data
            return view('admin.pages.edit', ['page'=>$page]);
        } else {
            // Return error if ID not found
            session()->flash('success','ID not Found');
            return response()->json([
                'status'=>false,
                'message'=>'ID not Found'
            ]);
        }
    }

    /**
     * Update an existing page by ID.
     * Validates required fields: name and unique slug.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:pages',
        ]);

        if ($validator->passes()) {
            // Find the page
            $user = Pages::find($id);

            // Update page details
            $user->name = $request->name;
            $user->slug = $request->slug;
            $user->content = $request->content;
            $user->save();

            // Flash success message
            session()->flash('success','Page updated successfully.');

            return response()->json([
                'status'=>true,
                'message'=>'Page updated successfully.'
            ]);
        } else {
            // Return validation errors
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    /**
     * Delete a page by ID.
     * Returns JSON response with status and message.
     */
    public function destroy($pageId, Request $request)
    {
        // Find page
        $page = Pages::find($pageId);

        if(empty($page)) {
            // Page not found
            $request->session()->flash('error','Page not Found');
            return response()->json([
                'status'=>true,
                'message'=>'Page not Found'
            ]);
        }

        // Delete page
        $page->delete();

        // Flash deletion message
        $request->session()->flash('danger','Page Deleted Successfully');

        return response()->json([
            'status'=>true,
            'message'=>'Page Deleted Successfully'
        ]);
    }
}
