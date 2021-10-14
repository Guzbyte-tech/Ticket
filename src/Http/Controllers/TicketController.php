<?php

namespace Guzbyte\Ticket\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Guzbyte\Ticket\Helper\Helper;
use Guzbyte\Ticket\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Guzbyte\Ticket\Models\TicketAgent;
use Guzbyte\Ticket\Models\TicketComment;
use Guzbyte\Ticket\Models\TicketCategory;
use Guzbyte\Ticket\Models\TicketPriority;
use Illuminate\Support\Facades\Validator;
use Guzbyte\Ticket\Models\TicketAttachment;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::whereUserId(auth()->user()->id)->orderBy("id", "desc")->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadMessages(auth()->user()->id,$ticket->id)));
        }
        //return dd($response);
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        return view("ticket::ticket.users.index")->with([
            "count" => 1,
            "ticketCount" => $ticketCount,
            "tickets" => $response,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = TicketCategory::all();
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        return view("ticket::ticket.users.create")->with([
            "categories" => $categories,
            "ticketCount" => 0,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => ['required'],
            "email" => ['required'],
            "title" => ["required"],
            "category" => ["required"],
            "message" => ["required"],
            "attachment.*" => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff |max:2048']
        ]);
        $validator->validate();
        $data = [];
        $assignedAgent = $this->autoAssignAgent($request->category);
        if($request->hasfile('attachment'))
         {
            foreach($request->file('attachment') as $file)
            {
                $name = time().'.'.$file->extension();
                $file->move(public_path().'/guz_ticket/attachment', $name);  
                $data[] = $name;  
            }
         }

        $ticket = Ticket::create([
            "title" => $request->title,
            "user_id" => auth()->user()->id,
            "name" => $request->name,
            "email" => $request->email,
            "title" => $request->title,
            "slug" => Str::slug($request->title),
            "category_id" => $request->category,
            "status_id" => 1,
            "agent_id" => $assignedAgent,
            "attachment" =>  json_encode($data),
            "message" => $request->message
        ]);

        

        $agentUserId = TicketAgent::find($assignedAgent)->user_id;
        $agentEmail = User::find($agentUserId)->email;
        $agentName = User::find($agentUserId)->name;
        $content = [
            "ticket" => $ticket,
            "content" => $request->message,
            "agent" => $agentName,
         ];

        Mail::send('ticket::emails.agent-reply', $content, function($message) use ($agentEmail, $agentName, $request) {
            $message->to($agentEmail, $agentName)->subject
               ("New Ticket- ".$request->title.'');
            $message->from(config("ticket.mail_from"), config("ticket.app_name"));
         });

        

        return redirect()->route("guzbyte.ticket.index")->with([
            "success" => "Your ticket has been recieved we will get back to you shortly",
            "ticketCount" => 0,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $slug)
    {

        
        $ticket = Ticket::findOrFail($id);
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        $ticketComments = TicketComment::whereTicketId($id)->get();
        $priorities = TicketPriority::all();
        TicketComment::whereTicketId($id)->update([
            "user_read" => 1,
        ]);
        return \view("ticket::ticket.users.show")->with([
            "ticket" => $ticket,
            "ticketCount" => $ticketCount,
            "ticketAgentCount" => 0,
            "email" => $ticket->email,
            "comments" => $ticketComments,
            "id" => $id,
            "slug" => $slug,
            "priority" => $priorities,
        ]);
    }

    public function reply(Request $request){
        $validator = Validator::make($request->all(), [
            "message" => ["required"],
            "priority" => ["nullable"],
            "attachments.*" => ["nullable", "mimes:jpg,jpeg,png,bmp,tiff", "max:2048"],
            "id" => ['required']
        ]);
        $validator->validate();
        $data = [];
        $id = $request->id;
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
            "agent_id" => $ticket->agent_id,
            "attachment" => json_encode($data),
            "user_read" => true,
            "message" => $request->message,
            "sender" => 0
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
         $agentEmail = User::find($ticket->agent_id)->email;
         $agentName = User::find($ticket->agent_id)->name;
         Mail::send('ticket::emails.agent-reply', $content, function($message) use ($ticket, $agentEmail) {
            $message->to($agentEmail, config("ticket.mail_from_name"))->subject
               ("New Ticket Re-".$ticket->title.'');
            $message->from(auth()->user()->email, auth()->user()->name);
         });
        

        return redirect()->route("guzbyte.ticket.show", [
            "id" => $id,
            "slug" => $slug,
            "success" => "Your reply has been sent the user will be notified"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        return view("ticket::ticket.users.edit")->with([
            "ticket" => $ticket,
            "ticketCount" => $ticketCount,
            "categories" => TicketCategory::all(),
            "priorities" => TicketPriority::all()
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "name" => ['required'],
            "email" => ['required'],
            "title" => ["required"],
            "category" => ["required"],
            "message" => ["required"],
            "attachment.*" => ['nullable', 'mimes:jpg,jpeg,png,bmp,tiff |max:2048']
        ]);
        $validator->validate();
        $data = [];
        $ticket = Ticket::findOrFail($id);
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
            "name" => $request->name,
            "email" => $request->email,
            "slug" => Str::slug($request->title),
            "category_id" => $request->category,
            "attachment" =>  json_encode($data),
            "message" => $request->message
        ]);

        return redirect()->route("guzbyte.ticket.index")->with([
            "success" => "Your ticket has been recieved we will get back to you shortly",
            "ticketCount" => 0,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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

    public function close($id){
        if(Ticket::find($id)->exists()){
            Ticket::find($id)->update([
                "status_id" => 2,
            ]);
            return redirect()->route("guzbyte.ticket.index");
        }
        return abort(404);
    }

    public function opened(){
        $tickets = Ticket::whereUserId(auth()->user()->id)->whereStatusId(1)->orderBy("id", "desc")->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadMessages(auth()->user()->id,$ticket->id)));
        }
        //return dd($response);
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        return view("ticket::ticket.users.open")->with([
            "count" => 1,
            "ticketCount" => $ticketCount,
            "tickets" => $response,
        ]);
    }

    public function closed(){
        $tickets = Ticket::whereUserId(auth()->user()->id)->whereStatusId(2)->orderBy("id", "desc")->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadMessages(auth()->user()->id,$ticket->id)));
        }
        //return dd($response);
        $ticketCount = TicketComment::whereUserId(auth()->user()->id)->where("user_read", 0)->count();
        return view("ticket::ticket.users.open")->with([
            "count" => 1,
            "ticketCount" => $ticketCount,
            "tickets" => $response,
        ]);
    }
}