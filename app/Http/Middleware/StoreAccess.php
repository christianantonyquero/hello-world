<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthMiddleware;

use Closure;

use App\Services\StoreLineService;
use Illuminate\Support\Facades\Gate;

use App\User;
use App\Store;
use Illuminate\Support\Facades\Auth;

class StoreAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $store_auth_required = config('store.auth-required');

        if($store_auth_required && Auth::guest()) {
            return redirect()->route('login');
        }

        $store = $request->store;

        // Grab default store, if a store not passed in to the route
        if ( !$request->route('store') ) {
            //this is the default route grab the default store
            $store = Store::default_store();
        }

        // Store is Null Store - then store not found
        if (!$store) {
            abort(404, 'Store Not Found'); //Store not found
        }

        if( Gate::allows('view', $store) ) {
            return $next($request);
        } else {
            abort(403, 'Access Denied');
        }

        // // Handle null store
        // if(!$request->store) {
        //     abort(404); // Store not found
        // }

        // $user = auth()->user();

        // if( Gate::allows('view', $request->store) ) {
        //     return $next($request);
        // } else {
        //     if(!$store->isOpen()) {
        //         return response(['error' => 'Store not open yet.'], 403);
        //     } else {
        //         return response(['error' => 'Store Access Denied', 403]);
        //     }
        // }
        
        /* BEGIN comment out all STORE LINE CHECKING CODE

        // store line access not available
        if (!(new StoreLineService)->userHasAccess($request->store, auth()->user()) || !$request->store->isOpen()) {
            if($request->ajax() || $request->wantsJson()){
                return response(['error' => 'Store not open yet.'], 403);
            }
            else{
                return redirect(route('store.line', $request->store));
            }
        } 
        
        END Disable the Line code */
 
    }
}
