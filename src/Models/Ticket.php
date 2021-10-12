<?php

namespace Guzbyte\Ticket\Models;

use Illuminate\Database\Eloquent\Model;
use Guzbyte\Ticket\Models\TicketComment;

class Ticket extends Model
{
    protected $guarded = [];
    protected $table = "tickets";


    public function ticketComment(){
       /**
         * Get all of the comments for the Ticket
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
       
        return $this->hasMany(TicketComment::class, 'ticket_id');
        
    }
}
