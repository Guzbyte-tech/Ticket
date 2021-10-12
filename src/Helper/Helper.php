<?php

namespace Guzbyte\Ticket\Helper;

use Guzbyte\Ticket\Models\TicketComment;

class Helper{

    public function unreadMessages($userId, $ticketId){
        return TicketComment::whereTicketId($ticketId)->whereUserId($userId)->whereUserRead(0)->count();
    }

    public function unreadAgentMessages($agentId, $ticketId){
        return TicketComment::whereTicketId($ticketId)->whereAgentId($agentId)->whereAgentRead(0)->count();
    }

}