<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;

class IsUser
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
        if($request->user()->ticket_sub_admin !== 0 || $request->user()->ticket_super_admin !== 0){
            return abort(403);
        }
        return $next($request);
    }
}
