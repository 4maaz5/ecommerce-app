<?php

namespace App\Http\Controllers\admin;

use Nette\Utils\Image;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempImagesController extends Controller
{
    /**
     * Handle uploading of a temporary image.
     */
    public function create(Request $request)
    {
        // Get the uploaded image from the request
        $image = $request->image;

        // Check if an image was provided
        if (!empty($image)) {
            // Get the image extension
            $ext = $image->getClientOriginalExtension();

            // Generate a new unique name using timestamp
            $newName = time() . '.' . $ext;

            // Create a new TempImage record in the database
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            // Move the uploaded image to the public/temp folder
            $image->move(public_path() . '/temp', $newName);

            // Optional: generate a thumbnail for the image (commented out)
            // $sourcePath = public_path() . '/temp/' . $newName;
            // $destPath = public_path() . '/temp/thumb/' . $newName;
            // $image = Image::make($sourcePath);
            // $image->save($destPath);

            // Return JSON response with image details
            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'imagePath' => asset('/temp/thumb/' . $newName),
                'message' => 'Image Uploaded Successfully!'
            ]);
        }
    }
}
