<?php

namespace Guzbyte\Ticket\Models;

use Guzbyte\Ticket\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $guarded = [];
    protected $table = "ticket_comments";

    /**
     * Get the ticket that owns the TicketComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id');
    }
}
