<?php

use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home page route -- pulls up the default store
Route::middleware(['store-access'])->group(function() { 
    Route::get('/', 'Store\StoreHomeController@default_store')->name('home');
});

// Store Front-End
// ------------
Route::group([
    'prefix' => 'store/{store}',
    'namespace' => 'Store',
    'middleware' => ['store-access'],
], function () {
    
    // Store Index
    Route::get('/', 'StoreHomeController@show')->name('store');

    // APIs for SPA front-end
    // ----------------------
    Route::group([
        'prefix' => 'api',
        'namespace' => 'Api',
    ], function() {

        // Store Menu
        Route::get('/menu', 'StoreController@getPageMenu')->name('store.api.store-menu');
        Route::get('/mobile_menu', 'StoreController@getMobileMenu')->name('store.api.store-mobile-menu');

        // Product Categories
        Route::get('/categories', 'CategoryController@index')->name('store.api.categories');
        Route::get('/categories/{productCategory}', 'CategoryController@products')->name('store.api.categories.show');

        // Products
        Route::get('/products', 'ProductController@index')->name('store.api.products');
        Route::get('/products/{product}', 'ProductController@show')->name('store.api.products.show');
        Route::get('/products/suggested', 'ProductController@suggested')->name('store.api.products.suggested');

        // Store Pages
        Route::get('/pages', 'PageController@index')->name('store.api.pages');
        // make this an explicit route b/c Load Balander did not like optional param
        Route::get('/pages/load/{slug}', 'PageController@show')->name('store.api.page.show');

        // Songs / Music
        Route::get('/songs', 'StoreSongController@get_store_playlist')->name('store.songs');

        // Cart
        Route::get('/cart', 'CartController@index')->name('store.cart');
        Route::get('/cart/total-quantity', 'CartController@totalQuantity')->name('store.cart.total-quantity');
        Route::delete('/cart/clear', 'CartController@clear')->name('store.cart.clear');
        Route::post('/cart/add', 'CartController@add')->name('store.cart.add');
        Route::patch('/cart/{cartId}', 'CartController@update')->name('store.cart.update');
        Route::get('/cart/suggested', 'ProductController@suggested')->name('store.api.cart.suggested');

        // Photo Booth - Store Routes
        // **** -- these always require auth
        Route::middleware(['auth'])->group(function() {
            Route::get('/photo-booth/backdrops', 'PhotoBoothStoreController@getBackdrops')->name('store.photo-booth.get-backdrops');
            Route::post('/photo-booth/process-user-image', 'PhotoBoothStoreController@processUserImage')->name('store.photo-booth.process-user-image');
            Route::post('/photo-booth/save-user-image', 'PhotoBoothStoreController@saveUserImage')->name('store.photo-booth.save-user-image');
        });   
    });
});

// Publically shared photo booth image
Route::get('/photo-booth/{sharingID}', 'PhotoBooth\PhotoBoothController@show')->name('photo-booth.public-show');

// Authenctication routes -- add Honeypot
Route::middleware(ProtectAgainstSpam::class)->group(function() {
    Auth::routes(['register' => true]);
});


// Socialite Auth / OAuth.  These routes are also used for adding  a social provder to an existing (logged-in) user account
Route::get('/login/{provider}', 'Auth\SocialLoginController@redirectToProvider')->name('auth.socialite.login');
Route::get('/login/{provider}/callback', 'Auth\SocialLoginController@handleProviderCallback')->name('auth.socialite.callback');

// User Profile
Route::group([
    'prefix' => 'profile',
    'middleware' => ['auth']
], function() {

    // Main User Profile
    Route::get('/', 'UserProfileController@index')->name('user-profile.index');
    Route::patch('/', 'UserProfileController@update')->name('user-profile.update');
    Route::delete('/', 'UserProfileController@delete')->name('user-profile.delete');

    // Profile Photo
    Route::patch('profile-photo', 'UserProfileController@update_profile_photo')->name('user-profile.profile_photo.update');
    Route::delete('profile-photo', 'UserProfileController@delete_profile_photo')->name('user-profile.profile_photo.delete');

    Route::get('change-password', 'Auth\ChangePasswordController@change_password')->name('user-profile.password.change');
    Route::patch('change-password', 'Auth\ChangePasswordController@update_password')->name('user-profile.password.update');
});

// Admin Routes
// -----------------
Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin',
    'middleware' => ['admin-auth'],
], function () {

    // Global Admin
    Route::get('/', 'DashboardController@index')->name('admin');

    // Store Context Switching
    Route::get('/change-store-context/{store}', 'ChangeStoreContextController')->name('admin.change-store');

    // Stores
    Route::get('/stores', 'StoreController@index')->name('admin.stores');
    Route::post('/store', 'StoreController@create')->name('admin.stores.create');
    Route::get('/store/{store}', 'StoreController@show')->name('admin.stores.show');
    Route::post('/store/{store}', 'StoreController@update')->name('admin.stores.update');
    Route::delete('/store/delete/{store}', 'StoreController@destroy')->name('admin.stores.delete');

    // Products
    Route::get('/store/{store}/products', 'ProductController@index')->name('admin.products.index');
    Route::get('/store/{store}/products/create', 'ProductController@create')->name('admin.products.create');
    Route::post('/store/{store}/products', 'ProductController@store')->name('admin.products.store');
    Route::post('/store/{store}/products/delete-many/', 'ProductController@deleteMany')->name('admin.products.delete-many');
    Route::post('/store/{store}/products/update-publish-status-many/', 'ProductController@updatePublishStatusMany')->name('admin.products.update-publish-status-many');
    Route::get('/store/{store}/products/{product}/edit', 'ProductController@edit')->name('admin.products.edit');
    Route::post('/store/{store}/products/{product}', 'ProductController@update')->name('admin.products.update');
    Route::delete('/store/{store}/products/{product}/delete/', 'ProductController@destroy')->name('admin.products.delete');

    // Users
    Route::get('/users', 'UserController@index')->name('admin.users.index');
    Route::post('/users', 'UserController@create')->name('admin.users.create');
    Route::delete('/users/delete/{user}', 'UserController@destroy')->name('admin.users.delete');
    Route::get('/users/export', 'UserController@export')->name('admin.users.export');
    Route::get('/users/{user}', 'UserController@show')->name('admin.users.show');
    Route::patch('/users/{user}', 'UserController@update')->name('admin.users.update');

    // Store-level Admin
    Route::group([
        'namespace' => 'Store',
        'prefix' => 'store/{store}'
    ], function () {
        Route::get('/dashboard', 'DashboardController@index')->name('admin.store.dashboard');
        
        // Pages
        Route::get('/pages', 'PageController@index')->name('admin.store.pages');
        Route::get('/pages/new', 'PageController@new')->name('admin.store.pages.new');
        Route::post('/pages', 'PageController@create')->name('admin.store.pages.create');
        Route::get('/pages/{id}', 'PageController@edit')->name('admin.store.pages.edit');
        Route::post('/pages/{id}', 'PageController@update')->name('admin.store.pages.update');
        Route::delete('/pages/{id}/delete', 'PageController@destroy')->name('admin.store.pages.delete');
        
        // Hotspots
        Route::get('/pages/{page_id}/hotspots', 'HotspotController@index')->name('admin.store.pages.hotspots');
        Route::post('/pages/{page_id}/hotspots', 'HotspotController@create')->name('admin.store.pages.hotspots.create');
        Route::patch('/pages/{page_id}/hotspots/{id}', 'HotspotController@update')->name('admin.store.pages.hotspots.update');
        Route::delete('/pages/{page_id}/hotspots/{id}/delete', 'HotspotController@destroy')->name('admin.store.pages.hotspots.delete');
    
        // Songs
        Route::get('/songs', 'SongController@index')->name('admin.store.songs');
        Route::post('/songs', 'SongController@create')->name('admin.store.songs.create');
        Route::get('/songs/{id}', 'SongController@edit')->name('admin.store.songs.edit');
        Route::post('/songs/{id}', 'SongController@update')->name('admin.store.songs.update');
        Route::delete('/songs/{id}/delete', 'SongController@destroy')->name('admin.store.songs.delete');
    
         // Product Categories
        Route::get('/products-categories', 'ProductCategoryController@index')->name('admin.products.categories.index');
        Route::get('/products-categories/create', 'ProductCategoryController@create')->name('admin.products.categories.create');
        Route::post('/products-categories', 'ProductCategoryController@store')->name('admin.products.categories.store');
        Route::get('/products-categories/{productCategory}', 'ProductCategoryController@edit')->name('admin.products.categories.edit');
        Route::post('/products-categories/{productCategory}', 'ProductCategoryController@update')->name('admin.products.categories.update');
        Route::delete('/products-categories/{productCategory}/delete/', 'ProductCategoryController@destroy')->name('admin.products.categories.delete');

        // Photo Booth Data
        Route::get('/photo-booth', 'PhotoBoothController@index')->name('admin.photo-booth.index');
        Route::post('/photo-booth/backdrops', 'PhotoBoothController@backdrop_create')->name('admin.photo-booth.backdrop.create');
        Route::post('/photo-booth/backdrops/{backdropID}', 'PhotoBoothController@backdrop_update')->name('admin.photo-booth.backdrop.update');
        Route::delete('/photo-booth/backdrops/{backdropID}', 'PhotoBoothController@backdrop_delete')->name('admin.photo-booth.backdrop.delete');
        Route::get('/photo-booth/user-uploads', 'PhotoBoothController@user_upload_index')->name('admin.photo-booth.user_upload.index');
        Route::delete('/photo-booth/user-uploads/{id}', 'PhotoBoothController@user_upload_delete')->name('admin.photo-booth.user_upload.delete');
        
    });

});


// Random Pages (privacy policy, terms service, etc)
Route::get('/terms-service', 'StaticPagesController@terms_service')->name('terms-service');
Route::get('/privacy-policy', 'StaticPagesController@privacy_policy')->name('privacy-policy');
// Route::get('/shipping-returns', 'StaticPagesController@shipping_returns')->name('shipping-returns');

Route::get('/email-us', 'StaticPagesController@email_us')->name('email-us');
Route::post('/email-us', 'StaticPagesController@send_email')->name('send-email');
Route::get('/thank-you', 'StaticPagesController@thank_you')->name('thank-you');