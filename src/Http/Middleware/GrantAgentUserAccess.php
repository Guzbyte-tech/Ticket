<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use Guzbyte\Ticket\Models\Ticket;

class GrantAgentUserAccess
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
        $id = $request->id;
        if(!Ticket::find($id)->exists){
            return abort(404);
        }
        $ticket = Ticket::find($id);

        if($ticket->user_id !== auth()->user()->id && $ticket->agent_id !==  auth()->user()->id){
            return abort(403);
        }
        return $next($request);
    }
}
