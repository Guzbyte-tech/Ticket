<?php

namespace Guzbyte\Ticket\Helper;

use App\User;
use Guzbyte\Ticket\Models\TicketAgent;
use Guzbyte\Ticket\Models\TicketComment;

class Helper{

    public function unreadMessages($userId, $ticketId){
        return TicketComment::whereTicketId($ticketId)->whereUserId($userId)->whereUserRead(0)->count();
    }

    public function unreadAgentMessages($agentId, $ticketId){
        return TicketComment::whereTicketId($ticketId)->whereAgentId($agentId)->whereAgentRead(0)->count();
    }

    public function unreadSuperAgentMessages($ticketId){
        return TicketComment::whereTicketId($ticketId)->whereAgentAdminRead(0)->count();
    }

    public function getAgent($agentId){
        $user_id = TicketAgent::find($agentId);
        if(is_null($user_id)){
            return "NA";
        }
        $user = config('ticket.user')->find($user_id->user_id);
        return $user->name;
    }

}