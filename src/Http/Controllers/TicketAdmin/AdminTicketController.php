<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Guzbyte\Ticket\Helper\Helper;
use Guzbyte\Ticket\Models\Ticket;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Mail;
use Guzbyte\Ticket\Models\TicketAgent;
use Guzbyte\Ticket\Models\TicketComment;
use Guzbyte\Ticket\Models\TicketCategory;
use Guzbyte\Ticket\Models\TicketPriority;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Guzbyte\Ticket\Http\Controllers\BaseController;

class AdminTicketController extends BaseController
{
    public function index(){
        $tickets = Ticket::orderBy('id', 'desc')->take(20)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("detail", [
                "unread" => $helper->unreadSuperAgentMessages($ticket->id),
                "agent_name" => $helper->getAgent($ticket->agent_id),
                "user" => config('ticket.user')->find($ticket->user_id)->name
            ]));
        }

        $totalTicketRaised = Ticket::count();
        $totalOpenedTicket = Ticket::whereStatusId(1)->count();
        $totalClosedTicket = Ticket::whereStatusId(2)->count();
        
        return view("ticket::ticket.admin.ticket.index")->with([
            "tickets" => $response,
            "count" => 1,
            "totalTicketRaised" => $totalTicketRaised,
            "totalClosedTicket" => $totalClosedTicket,
            "totalOpenedTicket" => $totalOpenedTicket
        ]);
        
    }

    public function showTicket($id, $slug){
        $ticket = Ticket::findOrFail($id);
        //Get Messages
        $comments = TicketComment::whereTicketId($id)->get();
        $priorities = TicketPriority::all();
        TicketComment::whereTicketId($id)->update([
            "agent_admin_read" => 1,
        ]);
        $ticket->update([
            "admin_read" => 1,
        ]);
        return \view("ticket::ticket.admin.agent.show")->with([
            "ticket" => $ticket,
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
         
         TicketComment::create([
            "ticket_id" => $id,
            "user_id" => $user_id,
            "is_super_agent" => true,
            "attachment" => json_encode($data),
            "agent_read" => true,
            "message" => $request->message,
            "sender" => 1,
            "agent_admin_read" => 1
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
            "agent" => auth()->user()->name,
         ];
         Mail::send('ticket::emails.agent-reply', $content, function($message) use ($ticket) {
            $message->to($ticket->email, config("ticket.app_name"))->subject
               ("Ticket Reply- ".$ticket->title.'');
            $message->from(config("ticket.mail_from"), config("ticket.app_name"));
         });
        //return dd("done");
        return redirect()->route("guzbyte.admin.agent.ticket.show", [
            "id" => $id,
            "slug" => $slug,
            "success" => "Your reply has been sent the user will be notified"
        ]);
    }

    public function closeTicket($id){
        Ticket::findOrFail($id)->update([
            "status_id" => 2,
        ]);
        return redirect()->back()->with([
            "success" => "Ticket closed successfully"
        ]);
    }

    public function edit($id){
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            "admin_read" => 1
        ]);
        $ticketAgentCount = TicketComment::whereAgentId($id)->where("agent_read", 0)->count();
        //return dd($ticket);
        return view("ticket::ticket.admin.ticket.edit")->with([
            "ticket" => $ticket,
            "ticketAgentCount" => $ticketAgentCount,
            "priorities" => TicketPriority::all(),
            "categories" => TicketCategory::all(),
            "agents" => TicketAgent::all(),
        ]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            "title" => ["required"],
            "category" => ["required"],
            "message" => ["required"],
            "priority_id" => ["nullable"],
            "agent" => ["nullable"],
            "attachment.*" => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff |max:2048']
        ]);
        $validator->validate();
        $data = [];
        $ticket = Ticket::findOrFail($id);
        $priority_id = $request->priority_id;
        $category_id =$request->category;
        $assignedAgent = $request->agent;
        $oldAgent = $ticket->agent_id;

        // if($ticket->category_id != $category_id){
        //     $assignedAgent = $this->autoAssignAgent($request->category);
        // }

        if($request->hasfile('attachment'))
         {
            foreach($request->file('attachment') as $file)
            {
                $name = time().'.'.$file->extension();
                $file->move(public_path().'/guz_ticket/attachment', $name);  
                $data[] = $name;  
            }
         }

        //return dd(intval($ticket->agent_id) ."==". intval($assignedAgent));

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
        $agentName = config('ticket.user')->find(TicketAgent::find($assignedAgent)->user_id)->name;
        $agentEmail = config('ticket.user')->find(TicketAgent::find($assignedAgent)->user_id)->email;
        
        if($oldAgent !== $assignedAgent){
            //return "jam here";
            $content = [
                "ticket" => $ticket,
                "content" => $request->message,
                "agent" => $agentName,
             ];
    
            Mail::send('ticket::emails.agent-reply', $content, function($message) use ($agentEmail, $agentName, $request) {
                $message->to($agentEmail, $agentName)->subject
                   ("New Ticket Assigned - ".$request->title.'');
                $message->from(config("ticket.mail_from"), config("ticket.app_name"));
             });
        }

        return redirect()->route("guzbyte.admin.ticket.index")->with([
            "success" => "Ticket updated successfully",
            "ticketAgentCount" => $ticketAgentCount,
        ]);
    }

    public function assign($ticket_id){
        $ticket = Ticket::find($ticket_id);
        $agents = TicketAgent::all();
        return view("ticket::ticket.admin.ticket.assign")->with([
            "ticket" => $ticket,
            "agents" => $agents

        ]);
    }

    public function updateTicketAgent(Request $request, $ticket_id){
        $validator = Validator::make($request->all(), [
            "agent" => ["required"]
        ]);
        $validator->validate();
        $ticket = Ticket::findOrFail($ticket_id);
        $ticketAgent = TicketAgent::findorFail($request->agent);
        $agentEmail = config('ticket.user')->find($ticketAgent->user_id)->email;
        $agentName = config('ticket.user')->find($ticketAgent->user_id)->name;


        $ticket->update([
            "agent_id" => $request->agent
        ]);
        $content = [
            "ticket" => $ticket,
            "content" => $ticket->message,
            "agent" => config('ticket.user')->find($ticketAgent->user_id)->name,
         ];

        Mail::send('ticket::emails.agent-reply', $content, function($message) use ($agentEmail, $agentName, $ticket) {
            $message->to($agentEmail, $agentName)->subject
               ("New Ticket Assigned - ".$ticket->title);
            $message->from(config("ticket.mail_from"), config("ticket.app_name"));
         });
         return redirect()->route("guzbyte.admin.ticket.index")->with([
             "success" => "Ticket assigned to Agent: $agentName successfully"
         ]);
    }

    public function openedTicket(){
        $tickets = Ticket::whereStatusId(1)->orderBy('id', 'desc')->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("detail", [
                "unread" => $helper->unreadSuperAgentMessages($ticket->id),
                "agent_name" => $helper->getAgent($ticket->agent_id),
                "user" => config('ticket.user')->find($ticket->user_id)->name
            ]));
        }

        $totalTicketRaised = Ticket::count();
        $totalOpenedTicket = Ticket::whereStatusId(1)->count();
        $totalClosedTicket = Ticket::whereStatusId(2)->count();
        //return dd($this->paginate($response, $perPage = 2, $page = null, $options = []));
        return view("ticket::ticket.admin.ticket.open")->with([
            "tickets" => $this->paginate($response, $perPage = 25, $page = null, $options = []),
            "count" => 1,
        ]);
    }

    public function closedTicket(){
        $tickets = Ticket::whereStatusId(2)->orderBy('id', 'desc')->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("detail", [
                "unread" => $helper->unreadSuperAgentMessages($ticket->id),
                "agent_name" => $helper->getAgent($ticket->agent_id),
                "user" => config('ticket.user')->find($ticket->user_id)->name
            ]));
        }

        $totalTicketRaised = Ticket::count();
        $totalOpenedTicket = Ticket::whereStatusId(1)->count();
        $totalClosedTicket = Ticket::whereStatusId(2)->count();
        
        return view("ticket::ticket.admin.ticket.close")->with([
            "tickets" => $this->paginate($response, $perPage = 25, $page = null, $options = []),
            "count" => 1,
        ]);
    }

    public function allTicket(){
        $tickets = Ticket::orderBy('id', 'desc')->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("detail", [
                "unread" => $helper->unreadSuperAgentMessages($ticket->id),
                "agent_name" => $helper->getAgent($ticket->agent_id),
                "user" => config('ticket.user')->find($ticket->user_id)->name
            ]));
        }

        $totalTicketRaised = Ticket::count();
        $totalOpenedTicket = Ticket::whereStatusId(1)->count();
        $totalClosedTicket = Ticket::whereStatusId(2)->count();
        
        return view("ticket::ticket.admin.ticket.all")->with([
            "tickets" => $this->paginate($response, $perPage = 25, $page = null, $options = []),
            "count" => 1,
        ]);
    }

    public function new(){
        $tickets = Ticket::whereAdminRead(0)->orderBy('id', 'desc')->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("detail", [
                "unread" => $helper->unreadSuperAgentMessages($ticket->id),
                "agent_name" => $helper->getAgent($ticket->agent_id),
                "user" => config('ticket.user')->find($ticket->user_id)->name
            ]));
        }

        $totalTicketRaised = Ticket::count();
        $totalOpenedTicket = Ticket::whereStatusId(1)->count();
        $totalClosedTicket = Ticket::whereStatusId(2)->count();
        
        return view("ticket::ticket.admin.ticket.new")->with([
            "tickets" => $this->paginate($response, $perPage = 25, $page = null, $options = []),
            "count" => 1,
        ]);
    }


/**
  * Gera a paginação dos itens de um array ou collection.
  *
  * @param array|Collection      $items
  * @param int   $perPage
  * @param int  $page
  * @param array $options
  *
  * @return LengthAwarePaginator
  */
    public function paginate($items, $perPage = 25, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

