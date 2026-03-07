<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Store;
use App\StorePage;

use App\Http\Requests\Admin\CreateStorePageRequest;
use App\Http\Requests\Admin\UpdateStorePageRequest;

class PageController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $pages = $store->pages()->where('parent_id', null)->simplePaginate(25);
        return view('admin.store.pages.index', ['pages' => $pages, 'store' => $store]);
    }

    public function new(Request $request, Store $store)
    {
        $allPages = $pages = $store->pages()->orderBy('name')->get();
        return view('admin.store.pages.new', ['allPages' => $allPages, 'store' => $store]);
    }

    public function create(CreateStorePageRequest $request, Store $store)
    {
        $validated = $request->validated();

        if( $request->input('hide_from_menu', false) ) {
            $hide_from_menu = true;
        } else {
            $hide_from_menu = false;
        }

        $page = new StorePage([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'minimum_age' => $validated['minimum_age'] ?? null,
            'html_content' => $validated['html_content'] ?? null,
            'background_video' => $validated['background_video'] ?? null,
            'intro_video' => $validated['intro_video'] ?? null,
            'store_id' => $store->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'menu_display_order' => $validated['menu_display_order'],
            'hide_from_menu' => $hide_from_menu,
        ]);

        // Add the background image if needed
        if( $validated['background_image'] ?? false ) {
            $page->background_image = $request->file('background_image')->store('store_pages/background_images');
        }

        $page->save();

        flash('Store Page Created');
        return response()->json(['success' => true, 'store_page' => $page]);
    }

    public function edit(Request $request, Store $store)
    {
        $page = StorePage::with('hotspots')->findOrFail($request->id);
        $otherPages = $store->pages()->whereNotIn('id', [$page->id])->orderBy('name')->get();
        return view('admin.store.pages.edit', ['page' => $page, 
            'otherPages' => $otherPages]);
    }

    public function update(UpdateStorePageRequest $request, Store $store)
    {
        $page = StorePage::with('hotspots')->findOrFail($request->id);

        $validated = $request->validated();

        if( $request->input('hide_from_menu', false) ) {
            $hide_from_menu = true;
        } else {
            $hide_from_menu = false;
        }

        if( array_key_exists('parent_id', $validated) && 
            $validated['parent_id'] == $page->id) {
            // Don't allow parent ids to your self
            throw ValidationException::withMessages(['parent_id' => 'A Page cannot be its own parent.']);
        }
    
        // Update Page
        $page->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'minimum_age' => $validated['minimum_age'] ?? null,
            'html_content' => $validated['html_content'] ?? null,
            'background_video' => $validated['background_video'] ?? null,
            'intro_video' => $validated['intro_video'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'menu_display_order' => $validated['menu_display_order'],
            'hide_from_menu' => $hide_from_menu,
        ]);

        // Update the background image if needed
        if( $validated['background_image'] ?? false ) {
            $page->background_image = $request->file('background_image')->store('store_pages/background_images');
            $page->save();
        }
        

        flash('Store Page Updated');
        return response()->json(['success' => true, 'store_page' => $page]);
    }
    public function destroy(Request $request)
    {
        $page = StorePage::findOrFail($request->id);
        $page->delete();
        flash('Store Page Deleted');
        return redirect(route('admin.store.pages', [$page->store]));
    }
}
