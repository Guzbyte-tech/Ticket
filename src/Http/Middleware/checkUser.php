<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use Guzbyte\Ticket\Models\Ticket;

class checkUser
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
        $user_id = Ticket::find($request->id)->user_id;
        if($user_id == auth()->user()->id){
            return $next($request);
        }
        return abort(403);
    }
}
