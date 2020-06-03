<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
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
        if(!Auth::guest() && Auth::user()->role=='admin'){
            return $next($request);
        }else {
            return redirect(route('manage.login'))->with('error',"Icaze Verilmir");
        }

        return redirect(route('manage.login'));
    }
}
