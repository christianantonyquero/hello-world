<?php

namespace App\Http\Controllers\Store\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

use Image;

use App\PhotoBoothBackdrop;
use App\PhotoBoothUserImage;
use App\Store;
use App\Rules\Base64Image;

// Private API Photo Booth controller for a 'store
class PhotoBoothStoreController extends Controller
{
    // Return background images
    public function getBackdrops(Request $request, Store $store)
    {
        $backdrops = PhotoBoothBackdrop::where('store_id', $store->id)->orderBy('display_order')->get();
        
        return response()->json([
            'backdrops' => $backdrops
        ]);
    }

    // Process the user images - autocrop & remove bg
    public function processUserImage(Request $request, Store $store)
    {
        $width = PhotoBoothBackdrop::WIDTH();
        $height = PhotoBoothBackdrop::HEIGHT();

        $validated = $request->validate([
            'userPhoto' => ['required', new Base64Image],
        ]);

        // Read in the webcam image (it is base64 not a file)
        $userImage = Image::make($request->input('userPhoto'));
        $userImage->fit($width, $height);

        $encodedImage = $userImage->encode('data-url')->encoded;
    
        $outImage = 'data:image/png;base64,' . removebg()->base64($encodedImage)->body([
            'size' => 'full'
        ])->getBase64();
        
        return response()->json([
            'out_image' =>  $outImage,
        ]);
    }

    // Save the user final user image, return url
    public function saveUserImage(Request $request, Store $store)
    {
        $validated = $request->validate([
            'userPhoto' => ['required', new Base64Image],
        ]);

        $user = auth()->user();

        // Turn the image into an 80% quality JPG, get a unique file name and save it
        $userImage = Image::make($request->input('userPhoto'))->encode('jpg', 80);

        $userImageFileName = 'photo-booth/user-images/' . self::unique_name() . '.jpg';
        Storage::put($userImageFileName, $userImage);

        //Storage::putFile('photo-booth/user-images/')

        //$image_has = md5($userImage->_toString());


        $photoBoothUserImage = new PhotoBoothUserImage([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'sharing_id' => (string) Str::orderedUuid(),
            'image' => $userImageFileName,
        ]);

        $photoBoothUserImage->save();

        return response()->json([
            'userPhoto' => $photoBoothUserImage
        ]);
    }

    public static function unique_name() {
        return (string)uniqid(time());
    }
}
