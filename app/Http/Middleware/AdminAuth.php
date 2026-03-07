<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as AuthMiddleware;

use Closure;

class AdminAuth extends AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        if (!auth()->user()->is_admin) {
            return redirect('/');
        }

        return $next($request);
    }
}
