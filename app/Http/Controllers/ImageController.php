<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function images()
    {
        $images = Image::all();
        if ($images->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Resimler başarıyla getirildi!',
                'images' => $images
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Resimler bulunamadı!'
            ]);
        }
    }
    public function delete($id)
    {
        $image = Image::find($id);
        if ($image) {
            $path = str_replace('/storage/', 'public/', $image->image);
            Storage::delete($path);
            $image->delete();
            return response()->json([
                'status' => true,
                'message' => 'Resim başarıyla silindi!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Resim bulunamadı!'
            ]);
        }
    }
}
