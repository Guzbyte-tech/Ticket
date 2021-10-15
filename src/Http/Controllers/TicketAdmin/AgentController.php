<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use App\User;
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
use Guzbyte\Ticket\Http\Controllers\BaseController;

class AgentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agents = TicketAgent::all();
        return view("ticket::ticket.admin.agent.index")->with([
            "agents" => $agents,
            "count" => 1
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
        return view("ticket::ticket.admin.agent.create")->with([
            "categories" => $categories
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
            "email" => ['required', 'email'],
            "category" => ["required"]
        ]);
        $email = $request->email;
        $user = User::whereEmail($email)->get()->first();
        $validator->after(function($validator) use ($request, $user, $email){
            if(is_null($user)){
                $validator->errors()->add("email", "User not found");
            }
            if(!is_null($user)){
                if($user->ticket_super_admin == 1){
                    $validator->errors()->add("email", "User is a ticket super agent");
                }
                if($user->ticket_sub_admin == 1){
                    $validator->errors()->add("email", "User is a already ticket agent");
                }
            }
            
        });
        $validator->validate();
        User::whereEmail($email)->update([
            "ticket_sub_admin" => true
        ]);
        TicketAgent::create([
            "user_id" => $user->id,
            "category" => $request->category
        ]);
        return redirect()->back()->with([
            "success" => "$user->name added as agent successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $agent = TicketAgent::findOrFail($id);
        $category = TicketCategory::all();
        return view("ticket::ticket.admin.agent.edit")->with([
            "agent" => $agent,
            "categories" => $category,
            "email" => User::find($agent->user_id)->email,
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
            "email" => ['required', 'email'],
            "category" => ["required"]
        ]);
        $email = $request->email;
        $user = User::whereEmail($email)->get()->first();
        $agents = TicketAgent::find($id);
        $validator->after(function($validator) use ($request, $user, $email, $id, $agents){
            if(is_null($user)){
                $validator->errors()->add("email", "User not found");
            }
            if(!is_null($user)){
                if($user->ticket_super_admin == 1){
                    $validator->errors()->add("email", "User is a ticket super agent");
                }
                if($user->ticket_sub_admin !== 1){
                    $validator->errors()->add("email", "User is not ticket agent");
                }

                if($user->id !== $agents->user_id){
                    $validator->errors()->add("email", "User mismatch");
                }
            }
            
        });
        $validator->validate();
        User::whereEmail($email)->update([
            "ticket_sub_admin" => true
        ]);
        TicketAgent::findOrFail($id)->update([
            "user_id" => $user->id,
            "category" => $request->category
        ]);
        return redirect()->route("guzbyte.admin.ticket.agent.index")->with([
            "success" => "Agent $user->name updated successfully"
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

    public function deactivate($id){
        $agent = TicketAgent::findOrFail($id)->user_id;
        $agentName = User::findOrFail($agent)->name;
        TicketAgent::findOrFail($id)->update([
            "is_active" => false
        ]);
        return redirect()->route("guzbyte.admin.ticket.agent.index")->with([
            "success" => "Agent $agentName de-activated successfully"
        ]);
    }

    public function activate($id){
        $agent = TicketAgent::findOrFail($id)->user_id;
        $agentName = User::findOrFail($agent)->name;
        TicketAgent::findOrFail($id)->update([
            "is_active" => true
        ]);
        return redirect()->route("guzbyte.admin.ticket.agent.index")->with([
            "success" => "Agent $agentName activated successfully"
        ]);
    }

    public function getAgentTicket($agent_id){
        $tickets = Ticket::whereAgentId($agent_id)->get();
        $helper = new Helper();
        $response = \collect();
        foreach($tickets as $ticket){
            $response->push($ticket->setAttribute("unread", $helper->unreadSuperAgentMessages($ticket->id)));
        }
        return view("ticket::ticket.admin.agent.tickets")->with([
            "tickets" => $response,
            "count" => 1,
        ]);
    }

    

    
    
}
