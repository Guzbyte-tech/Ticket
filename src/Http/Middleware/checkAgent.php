<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use Guzbyte\Ticket\Models\Ticket;
use Guzbyte\Ticket\Models\TicketAgent;

class checkAgent
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
        $agent_id = Ticket::find($request->id)->agent_id;
        $agentUserId = TicketAgent::find($agent_id)->user_id;
        if($agentUserId == auth()->user()->id){
            return $next($request);
        }
        return abort(403);
        
    }
}
