<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;

class IsTicketSuperAdmin
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
        if(!$request->user()->ticket_super_admin){
            return abort(403);
        }
        return $next($request);
    }
}
