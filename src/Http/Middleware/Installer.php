<?php

namespace Guzbyte\Ticket\Http\Middleware;

use Closure;
use App\User;

class Installer
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
        $install = User::whereTicketSuperAdmin(1)->count();
        if($install !== 1){
            return \redirect()->route("guzbyte.ticket.install");
        }
        return $next($request);
    }
}
