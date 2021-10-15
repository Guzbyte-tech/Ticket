<?php

namespace Guzbyte\Ticket\Http\Controllers\TicketAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Guzbyte\Ticket\Models\TicketCategory;
use Illuminate\Support\Facades\Validator;
use Guzbyte\Ticket\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = TicketCategory::orderBy("id", "DESC")->paginate(25);
        return view("ticket::ticket.admin.category.index")->with([
            "categories" => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("ticket::ticket.admin.category.create");
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
            "category_name" => ['required','max:191']
        ]);
        $validator->validate();
        TicketCategory::create([
            "name" => $request->category_name
        ]);
        return redirect()->back()->with([
            "success" => "Category $request->category_name created successfully"
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
        $category = TicketCategory::findOrFail($id);
        return view("ticket::ticket.admin.category.edit")->with([
            "category" => $category
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
            "category_name" => ['required','max:191']
        ]);
        $validator->validate();
        TicketCategory::find($id)->update([
            "name" => $request->category_name
        ]);
        return redirect()->back()->with([
            "success" => "Category update successfully"
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
        $category = TicketCategory::findOrFail($id);
        $category->delete();
        return redirect()->back()->with([
            "success" => "Category delete successfully"
            ]);
        return dd($id);
    }
}
