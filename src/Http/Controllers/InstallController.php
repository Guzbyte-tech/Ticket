<?php

namespace Guzbyte\Ticket\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstallController extends BaseController
{
    public function index(){
        if(config('ticket.user')->whereTicketSuperAdmin(1)->count() > 0){
            return redirect()->route("guzbyte.ticket.install.success");
        }
        return view("ticket::ticket.install");
    }

    public function process(Request $request){
        $validator = Validator::make($request->all(), [
            "email" => ["required", "email"]
        ]);
        if(!config('ticket.user')->whereEmail($request->email)->exists()){
            $validator->after(function($validator) use ($request){
                $validator->errors()->add("email", "No user found with this email address");
            });
        }
        $validator->validate();
        config('ticket.user')->query()->update([
            "ticket_super_admin" => false,
        ]);
        config('ticket.user')->whereEmail($request->email)->update([
            "ticket_super_admin" => true,
        ]);

        return redirect()->intended(route("guzbyte.ticket.install.success"));
        

        return dd($request->all());
    }

    public function success(){
        return view("ticket::ticket.success");
    }
}
