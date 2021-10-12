<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Guzbyte\Ticket\Models\TicketPriority;
use Illuminate\Support\Facades\Validator;


class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priorities = TicketPriority::orderBy("id", "desc")->get();
        return view("ticket::ticket.admin.priority.index")->with([
            "priorities" => $priorities,
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
        return view("ticket::ticket.admin.priority.create");
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
            "color" => ["nullable"]
        ]);
        $validator->validate();

        TicketPriority::create($request->all());
        return redirect()->back()->with([
            "success" => "Ticket Priority Created Successfully",
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
        $priority = TicketPriority::findOrFail($id);
        return \view("ticket::ticket.admin.priority.edit")->with([
            "priority" => $priority
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
            "color" => ["nullable"]
        ]);
        $validator->validate();

        TicketPriority::findOrFail($id)->update($request->all());
        return redirect()->route("guzbyte.admin.ticket.prioriy.index")->with([
            "success" => "Ticket Priority Updated Successfully",
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
        TicketPriority::findOrFail($id)->delete();
        return redirect()->route("guzbyte.admin.ticket.prioriy.index")->with([
            "success" => "Ticket Priority Deleted Successfully",
        ]);
    }
}
