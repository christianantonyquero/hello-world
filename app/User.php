<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Image;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    const PROFILE_IMG_DIR = 'users/profile-images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'display_name', 'date_of_birth', 'phone',
        'address', 'address_2', 'city', 'state_province', 'postal_code', 'country',
        'profile_photo', 'opt_in_newsletter', 'last_login_at', 'login_count',
        'umusic_email_opt_in', 'umusic_sms_opt_in',
    ];

    protected $appends = ['profile_photo_url'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_admin' => 'boolean',
        'opt_in_newsletter' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'is_tester' => 'boolean',
    ];

    public function accounts() {
        return $this->hasMany('App\LinkedSocialAccount');
    }

    public function addSocialAccount(string $provider, string $provider_id) {
        $this->accounts()->create([
            'provider_id' => $provider_id,
            'provider_name' => $provider,
        ]);
    }

    // helpers
    public function getProfilePhotoUrlAttribute()
    {
        if($this->profile_photo) {
            return Storage::url($this->profile_photo);
        } else {
            return "";
        }
    }

    public function updateProfilePhoto(string $profileImage)
    {
        $path = Storage::putFile(User::PROFILE_IMG_DIR, $profileImage);
        $this->profile_photo = $path;
        $this->save();
    }

    public function deleteProfilePhoto()
    {
        // Delete existing image file
        $profile_photo = $this->profile_photo;
        $rtn = Storage::delete($this->profile_photo);
        $this->profile_photo = null;
        $this->save();
    }

    // Create the user's profiel photo from a remote url
    public function saveRemoteProfilePhoto(string $photo_url)
    {
       try {
            // Get Image and convert it to a 75% quality jpeg
            $img = Image::make($photo_url)->encode('jpg', 75);
            $ext = '.jpg';

            $filename = "u-{$this->id}-" . Str::random(40);
            $filename = $filename . $ext;

            $img_path = self::PROFILE_IMG_DIR . "/" . $filename;
            $rtn = Storage::put($img_path, $img->stream());
            $this->profile_photo = $img_path;
            $this->save();
       }
        catch(\Exception $exp) {
            Log::error("Unable to save remote image for user: {$this->id} - [{$photo_url}]");
            Log::error($exp);
        }
    }

    // current admin store (controls sidebar context, etc)
    public function currentStore()
    {
        return $this->belongsTo(Store::class, 'current_store_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function switchStoreContext(Store $store)
    {
        $this->currentStore()->associate($store);
        $this->save();
    }

    public function storeList()
    {
        if ($this->is_admin || $this->is_tester ) {
            return Store::all();
        } else {
            return $this->stores;
        }
    }

    // simple array of ids/names for forms
    public function storeFormList()
    {
        return $this->storeList()->sortBy('name')->pluck('name', 'id')->toArray();
    }

    public function age()
    {
        return Carbon::now()->diffInYears($this->date_of_birth);
    }

}
