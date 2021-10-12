<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminTicketController extends Controller
{
    public function index(){
        return view("ticket::ticket.admin.index");
        
    }
}
