<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Store;
use App\PhotoBoothBackdrop;
use App\PhotoBoothUserImage;
use Illuminate\Support\Facades\Storage;

class PhotoBoothController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $backdrops = PhotoBoothBackdrop::where('store_id', $store->id)->orderBy('display_order')->get();
        $userImageCount = PhotoBoothUserImage::count();
        $mostRecentImages = PhotoBoothUserImage::with(['user'])->latest()->limit(5)->get();
        return view('admin.store.photo-booth.index', [
            'backdrops' => $backdrops, 
            'userImageCount' => $userImageCount,
            'mostRecentImages' => $mostRecentImages,
            'store' => $store]);
    }

    // Backdrop methods
    public function backdrop_create(Request $request, Store $store) 
    {
        // Create new backdrop
        $backdropWidth = PhotoBoothBackdrop::WIDTH();
        $backdropHeight = PhotoBoothBackdrop::HEIGHT();

        $validated = $request->validate([
            'image' => [
                'required', 'image', 'max:10000',
                Rule::dimensions()->width($backdropWidth)->height($backdropHeight)
            ],
            'display_order' => 'required|integer|gte:1',
            'caption' => 'nullable|string',
        ], [
            'dimensions' => "The backdrop image must be {$backdropWidth} x {$backdropHeight}"
        ]);

        $backdrop = new PhotoBoothBackdrop([
            'store_id' => $store->id,
            'caption' => $validated['caption'] ?? null,
            'display_order' => $validated['display_order'] ?? null,
        ], [
            'dimensions' => "The backdrop image must be {$backdropWidth} x {$backdropHeight}"
        ]);

        $backdrop->image = $request->file('image')->store('photo-booth/backdrops');
        $backdrop->save();

        return response()->json(['success' => true, 'backdrop' => $backdrop]);
    }

    public function backdrop_update(Request $request, Store $store, int $backdropID)
    {
        // Update existing backdrop
        $backdropWidth = PhotoBoothBackdrop::WIDTH();
        $backdropHeight = PhotoBoothBackdrop::HEIGHT();

        $backdrop = PhotoBoothBackdrop::where([
                ['id', $backdropID],
                ['store_id', $store->id]
            ])->firstOrFail();

        $validated = $request->validate([
            'image' => [
                'image', 'max:10000',
                Rule::dimensions()->width($backdropWidth)->height($backdropHeight)
            ],
            'display_order' => 'required|integer|gte:1',
            'caption' => 'nullable|string',
        ]);

        $backdrop->update([
            'caption' => $validated['caption'] ?? null,
            'display_order' => $validated['display_order'] ?? null,
        ]);

        // Update the image only if needed
        if( $validated['image'] ?? false ) {
            $backdrop->image = $request->file('image')->store('photo-booth/backdrops');
            $backdrop->save();
        }

        return response()->json(['success' => true, 'backdrop' => $backdrop]);
    }

    public function backdrop_delete(Request $request, Store $store, int $backdropID)
    {
        $backdrop = PhotoBoothBackdrop::where([
                ['id', $backdropID],
                ['store_id', $store->id]
            ])->firstOrFail();
        
        $backdrop->delete();
        
        return response()->json(['success' => true]);

    }

    public function user_upload_index(Request $request,Store $store)
    {
        $uploads = PhotoBoothUserImage::with(['user'])->latest()->paginate(10);
        return view('admin.store.photo-booth.uploads-index',[
            'uploads' => $uploads,
            'store' => $store,
        ]);
    }

    public function user_upload_delete(Request $request,Store $store)
    {
        // Delete the upload object and the file
        $upload = PhotoBoothUserImage::findOrFail($request->id);
        Storage::delete($upload->image);
        $upload->delete();
        flash('Store Page Deleted');
        return redirect(route('admin.photo-booth.user_upload.index', [$store]));
    }
}
