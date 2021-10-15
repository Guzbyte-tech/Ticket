<?php

namespace Guzbyte\Ticket\Http\Controllers;

use Illuminate\Http\Request;
use Guzbyte\Ticket\Models\Ticket;
use App\Http\Controllers\Controller;
use Guzbyte\Ticket\Models\TicketAgent;
use Guzbyte\Ticket\Models\TicketComment;

class BaseController extends Controller
{
    public function __construct(){

        $unreadAdminMsg = TicketComment::whereAgentAdminRead(0)->count();
        $agentCount = TicketAgent::count();
        $unreadAdminTicket = Ticket::whereAdminRead(0)->count();

        view()->share([
            "unreadAdminMsg" => $unreadAdminMsg,
            "agentCount" => $agentCount,
            "unreadTickets" => $unreadAdminTicket
        ]);
    }
}
