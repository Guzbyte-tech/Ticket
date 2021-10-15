<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Guzbyte\Ticket\Models\TicketStatus;
use Illuminate\Support\Facades\Validator;
use Guzbyte\Ticket\Http\Controllers\BaseController;

class StatusController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = TicketStatus::orderBy("id", "desc")->get();
        return view("ticket::ticket.admin.status.index")->with(
            [
                "statuses" => $statuses, 
                "count" => 1
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("ticket::ticket.admin.status.create");
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
            "name" => ["required", "unique:ticket_statuses"]
        ]);
        $validator->validate();
        TicketStatus::create($request->all());

        return redirect()->back()->with([
            "success" => "Status created successfully",
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
        $status = TicketStatus::findOrFail($id);
        return view("ticket::ticket.admin.status.edit")->with([
            "status" => $status
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
            "name" => ["required", "unique:ticket_statuses"]
        ]);
        $validator->validate();
        TicketStatus::findOrFail($id)->update($request->all());

        return redirect()->route("guzbyte.admin.ticket.status.index")->with([
            "success" => "Status updated successfully",
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
        TicketStatus::findOrFail($id)->delete();
        return redirect()->route("guzbyte.admin.ticket.status.index")->with([
            "success" => "Status deleted successfully", 
        ]);
    }
}
