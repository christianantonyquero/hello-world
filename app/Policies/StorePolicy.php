<?php

namespace App\Policies;

use App\Store;
use App\User;
// use Illuminate\Auth\Access\HandlesAuthorization;
// use Illuminate\Auth\Access\Response;

class StorePolicy
{
    // use HandlesAuthorization;
    public function before(User $user, $ability)
    {
        if($user->is_admin || $user->is_tester) {
            return true;
        }
    }
    

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function view(?User $user, Store $store)
    {
        // Admins and Testers always have access
        // if( $user && ($user->is_admin || $user->is_tester)) {
        //     return true;
        // }

        if($store->isOpen()) {
            return true;
        }
        
        return false;
    }
}
