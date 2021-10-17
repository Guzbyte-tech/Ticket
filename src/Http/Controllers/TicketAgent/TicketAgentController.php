<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAgent;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Guzbyte\Ticket\Helper\Helper;
use Guzbyte\Ticket\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Guzbyte\Ticket\Models\TicketAgent;
use Illuminate\Support\Facades\Config;
use Guzbyte\Ticket\Models\TicketComment;
use Guzbyte\Ticket\Models\TicketCategory;
use Guzbyte\Ticket\Models\TicketPriority;
use Illuminate\Support\Facades\Validator;
use Guzbyte\Ticket\Http\Controllers\BaseController;

class TicketAgentController extends BaseController
{
    public function index(){
        $id = TicketAgent::whereUserId(auth()->user()->id)->get()->first()->id;
        $tickets = Ticket::whereAgentId($id)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadAgentMessages($ticket->agent_id,$ticket->id)));
        }
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        return \view("ticket::ticket.agents.index")->with([
            "ticketAgentCount" => $ticketAgentCount,
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
        $ticket->update([
            "agent_read" => 1,
        ]);
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        return \view("ticket::ticket.agents.show")->with([
            "ticket" => $ticket,
            "ticketCount" => 0,
            "ticketAgentCount" => $ticketAgentCount,
            "comments" => $comments,
            "id" => $id,
            "slug" => $slug,
            "priority" => $priorities,
            "name" => config('ticket.user')->find($ticket->user_id)->name,
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
         $user = config('ticket.user')->find($user_id);
         $agent_id = TicketAgent::whereUserId(auth()->user()->id)->get()->first()->id;
         TicketComment::create([
            "ticket_id" => $id,
            "user_id" => $user_id,
            "agent_id" => $agent_id,
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
            "agent" => config('ticket.user')->find($ticket->agent_id)->name,
         ];
         Mail::send('ticket::emails.agent-reply', $content, function($message) use ($ticket) {
            $message->to($ticket->email, config("ticket.app_name"))->subject
               ("Ticket Reply-".$ticket->title.'');
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
                ""
            ]);
        }

        return abort(404);
    }

    public function edit($id){
        $ticket = Ticket::findOrFail($id);
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        return view("ticket::ticket.agents.edit")->with([
            "ticket" => $ticket,
            "ticketAgentCount" => $ticketAgentCount,
            "priorities" => TicketPriority::all(),
            "categories" => TicketCategory::all()
        ]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "title" => ["required"],
            "category" => ["required"],
            "message" => ["required"],
            "priority_id" => ["nullable"],
            "attachment.*" => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff |max:2048']
        ]);
        $validator->validate();
        $data = [];
        $ticket = Ticket::findOrFail($id);
        $priority_id = $request->priority_id;
        $category_id =$request->category;
        $assignedAgent = $ticket->agent_id;
        if($ticket->category_id != $category_id){
            $assignedAgent = $this->autoAssignAgent($request->category);
        }
        if($request->hasfile('attachment'))
         {
            foreach($request->file('attachment') as $file)
            {
                $name = time().'.'.$file->extension();
                $file->move(public_path().'/guz_ticket/attachment', $name);  
                $data[] = $name;  
            }
         }

        $ticket->update([
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "category_id" => $request->category,
            "attachment" =>  json_encode($data),
            "message" => $request->message,
            "priority_id" => $priority_id,
            "agent_id" => $assignedAgent
        ]);

        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();

        return redirect()->route("guzbyte.agent.ticket.index")->with([
            "success" => "Ticket updated successfully",
            "ticketAgentCount" => $ticketAgentCount,
        ]);
    }

    public function autoAssignAgent($category){
        $possibleAgents = TicketAgent::whereCategory($category)->get();
        if(count($possibleAgents) == 0){
            return null;
        }
        $agentsIDs = [];
        $summation = [];
        foreach($possibleAgents as $agents){
            $agentsIDs[] = $agents->id;
        }
        for ($i=0; $i < count($agentsIDs); $i++) { 
            $summ = Ticket::whereAgentId($agentsIDs[$i])->count();
            $summation[] = [
                "agentId_$agentsIDs[$i]" => $summ,
            ];
        }
        $newArray = [];
        for ($j=0; $j < count($summation); $j++) { 
            foreach($summation[$j] as $key => $su){
                $newArray += [$key => $su];
            } 
        }
        $id = 0;
        $agent = array_keys($newArray, min($newArray));
        if($agent > 0){
            $newKey = array_rand($agent, 1);
        }     
        $id  = $agent[$newKey];
        
        $agentID = explode("_", $id);
        return $agentID[1];

    } 

    public function opened(){
        $id = TicketAgent::whereUserId(auth()->user()->id)->get()->first()->id;
        $tickets = Ticket::whereAgentId($id)->whereStatusId(1)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadAgentMessages($ticket->agent_id,$ticket->id)));
        }
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        return \view("ticket::ticket.agents.open")->with([
            "ticketAgentCount" => $ticketAgentCount,
            "tickets" => $response,
            "count" => 1
        ]);
    }

    public function closed(){
        $id = TicketAgent::whereUserId(auth()->user()->id)->get()->first()->id;
        $tickets = Ticket::whereAgentId($id)->whereStatusId(2)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadAgentMessages($ticket->agent_id,$ticket->id)));
        }
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        return \view("ticket::ticket.agents.close")->with([
            "ticketAgentCount" => $ticketAgentCount,
            "tickets" => $response,
            "count" => 1
        ]);
    }
}
