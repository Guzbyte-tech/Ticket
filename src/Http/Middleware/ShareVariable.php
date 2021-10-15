<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;

class ShareVariable
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
        if(auth()->user()->is_ticket_super_admin){
            //
        }
        return $next($request);
    }
}
