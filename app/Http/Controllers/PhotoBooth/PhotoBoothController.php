<?php

namespace App\Http\Controllers\PhotoBooth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Image;
use App\PhotoBoothUserImage;

// Main Photo Booth Controller for sharing and publicly view photo booth uploaded images
class PhotoBoothController extends Controller
{
    public function show(Request $request, $sharingID) 
    {
        $photo = PhotoBoothUserImage::with(['store', 'user'])->where('sharing_id', $sharingID)->firstOrFail();
        return view('photo-booth/share', [
            'photo' => $photo,
            'current_user' => auth()->user(),
        ]);
    }
}
