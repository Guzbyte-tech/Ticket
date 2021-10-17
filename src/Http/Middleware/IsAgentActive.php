<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use Guzbyte\Ticket\Models\TicketAgent;

class IsAgentActive
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
        if(auth()->user()->ticket_sub_admin){
            if(TicketAgent::whereUserId(auth()->user()->id)->get()->first()->is_active !== 1){
                return abort(403);
            }
        }
        return $next($request);
    }
}
