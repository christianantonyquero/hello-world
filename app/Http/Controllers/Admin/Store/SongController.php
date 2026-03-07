<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Store;
use App\StoreSong;

use App\Http\Requests\Admin\CreateStoreSong;
use App\Http\Requests\Admin\UpdateStoreSong;

class SongController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $songs = $store->songs()->orderBy('play_order')->orderBy('track_name')->simplePaginate(20);
        return view('admin.store.songs.index', [
            'songs' => $songs,
            'store' => $store]);
    }

    public function create(CreateStoreSong $request, Store $store) {
        $validated = $request->validated();

        $song = new StoreSong([
            'track_name' => $validated['track_name'],
            'track_audio_file' => $validated['track_audio_file'],
            'artist_name' => $validated['artist_name'],
            'album_name' => $validated['album_name'] ?? '',
            'play_order' => $validated['play_order'],
            'store_id' => $store->id
        ]);
        
        // Save Artwork and Audio File
        if($validated['track_artwork'] ?? false) {
            $song->track_artwork = $request->file('track_artwork')->store('songs/art');
        }

        $song->save();

        flash('Song Created');
        return response()->json(['success' => true, 'store_song' => $song]);
    }

    public function edit(Request $request, Store $store) {
        $song = StoreSong::findOrFail($request->id);
        return view('admin.store.songs.edit', [
            'song' => $song,
            'store' => $store]);
    }

    public function update(UpdateStoreSong $request, Store $store) {
        $song = StoreSong::findOrFail($request->id);
        $validated = $request->validated();
        
        $song->update([
            'track_name' => $validated['track_name'],
            'track_audio_file' => $validated['track_audio_file'],
            'artist_name' => $validated['artist_name'],
            'album_name' => $validated['album_name'] ?? '',
            'play_order' => $validated['play_order'],
            'store_id' => $store->id
        ]);

        // Update artwork and audio track if needed
        if($validated['track_artwork'] ?? false) {
            $song->track_artwork = $request->file('track_artwork')->store('songs/art');
        }
        
        $song->save();

        flash('Song Updated');
        return response()->json(['success' => true, 'store_song' => $song]);
    }

    public function destroy(Request $request, Store $store) {
        $song = StoreSong::findOrFail($request->id);
        $song->delete();
        flash('Song Deleted');
        return redirect(route('admin.store.songs', [$store]));
    }
}
