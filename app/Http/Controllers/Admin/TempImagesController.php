<?php

namespace App\Http\Controllers\admin;


use Nette\Utils\Image;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TempImagesController extends Controller
{
    public function create(Request $request){


        $image=$request->image;
        if(!empty($image)){
            $ext=$image->getClientOriginalExtension();
            $newName=time().'.'.$ext;
            $tempImage=new TempImage();
            $tempImage->name=$newName;
            $tempImage->save();
            $image->move(public_path().'/temp',$newName);


            // //generate thumbnail
            // $sourcePath=public_path().'/temp/'.$newName;
            // $destPath=public_path().'/temp/thumb/'.$newName;
            // $image=Image::make($sourcePath);
            // $image->save($destPath);

            return response()->json([
                'status'=>true,
                'image_id'=>$tempImage->id,
                'imagePath'=>asset('/temp/thumb/'.$newName),
                'message'=>'Image Uploaded Successfully!'
            ]);
    }
}
}
