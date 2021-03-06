<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use Guzbyte\Ticket\Models\Ticket;
use Guzbyte\Ticket\Models\TicketAgent;

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
       if(auth()->user()->ticket_sub_admin){
            $ida = TicketAgent::whereUserId(auth()->user()->id)->get()->first()->id;
            if($ticket->agent_id != $ida){
                return abort(403);
            }
       }else if($ticket->user_id != auth()->user()->id){
            return abort(403);
        }
        return $next($request);
    }
}
