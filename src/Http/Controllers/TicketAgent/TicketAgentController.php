<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAgent;

use App\User;
use Illuminate\Http\Request;
use Guzbyte\Ticket\Helper\Helper;
use Guzbyte\Ticket\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Guzbyte\Ticket\Models\TicketComment;
use Guzbyte\Ticket\Models\TicketPriority;
use Illuminate\Support\Facades\Validator;

class TicketAgentController extends Controller
{
    public function index(){
        
        $tickets = Ticket::whereAgentId(auth()->user()->id)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadAgentMessages($ticket->agent_id,$ticket->id)));
        }
        $ticketAgentCount = TicketComment::whereAgentId(auth()->user()->id)->where("agent_read", 0)->count();
        return \view("ticket::ticket.agents.index")->with([
            "ticketAgentCount" => 0,
            "tickets" => $response,
            "count" => 1
        ]);
    }

    public function show($id, $slug){
        $ticket = Ticket::findOrFail($id);
        //Get Messages
        $comments = TicketComment::whereTicketId($id)->get();
        $priorities = TicketPriority::all();
        TicketComment::whereTicketId($id)->update([
            "agent_read" => 1,
        ]);
        return \view("ticket::ticket.agents.show")->with([
            "ticket" => $ticket,
            "ticketCount" => 0,
            "ticketAgentCount" => 0,
            "comments" => $comments,
            "id" => $id,
            "slug" => $slug,
            "priority" => $priorities,
            "name" => User::find($ticket->user_id)->name,
            "email" => $ticket->email
        ]);
    }

    public function reply(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "message" => ["required"],
            "priority" => ["nullable"],
            "attachments.*" => ["nullable", "mimes:jpg,jpeg,png,bmp,tiff", "max:2048"]
        ]);
        $validator->validate();
        $data = [];
        if($request->hasfile('attachment'))
         {
            foreach($request->file('attachment') as $file)
            {
                $name = time().'.'.$file->extension();
                $file->move(public_path().'/guz_ticket/attachment', $name);  
                $data[] = $name;  
            }
         }
         $ticket = Ticket::find($id);
         $user_id = Ticket::find($id)->user_id;
         $slug = Ticket::find($id)->slug;
         $user = User::find($user_id);
         TicketComment::create([
            "ticket_id" => $id,
            "user_id" => $user_id,
            "agent_id" => auth()->user()->id,
            "attachment" => json_encode($data),
            "agent_read" => true,
            "message" => $request->message,
            "sender" => 1
         ]);

         if(!is_null($request->priority)){
            Ticket::find($id)->update([
                "priority_id" => $request->priority
            ]);
         }

         $content = [
            "ticket" => Ticket::find($id),
            "user" => $user,
            "content" => $request->message,
            "agent" => User::find($ticket->agent_id)->name,
         ];
         Mail::send('ticket::emails.agent-reply', $content, function($message) use ($ticket) {
            $message->to($ticket->email, config("ticket.app_name"))->subject
               ($ticket->title.' - Reply');
            $message->from(config("ticket.mail_from"), config("ticket.app_name"));
         });
        

        return redirect()->route("guzbyte.ticket.agent.show", [
            "id" => $id,
            "slug" => $slug,
            "success" => "Your reply has been sent the user will be notified"
        ]);

    }

    public function close($id){
        if(Ticket::find($id)->exists()){
            Ticket::find($id)->update([
                "status_id" => 2,
            ]);
            return redirect()->route("guzbyte.agent.ticket.index")->with([
                "success" => "Ticket Closed successfully",
            ]);
        }
    }
}
