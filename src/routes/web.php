<?php
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Guzbyte\Ticket\Http\Controllers'], function(){

    Route::group(['middleware' => ['web']], function(){

        Route::get('ticket/install', 'InstallController@index')->name('guzbyte.ticket.install');
        Route::post('ticket/install', 'InstallController@process')->name('guzbyte.ticket.install.process');
        Route::get('ticket/install-complete', 'InstallController@success')->name('guzbyte.ticket.install.success');

    });
});
Route::group(['namespace' => 'Guzbyte\Ticket\Http\Controllers'], function(){
    Route::group(['middleware' => ['installer','web', 'auth', 'is_user']], function(){
        Route::get('ticket', 'TicketController@index')->name('guzbyte.ticket.index');
        Route::get('ticket/create', 'TicketController@create')->name('guzbyte.ticket.create');
        Route::post('ticket/store', 'TicketController@store')->name('guzbyte.ticket.store');
        Route::post('ticket/reply', 'TicketController@reply')->name("guzbyte.ticket.reply")->middleware(["checkUser"]);
        Route::get('ticket/show/{id}/{slug}', 'TicketController@show')->name('guzbyte.ticket.show')->middleware(["checkUser"]);
        Route::get('ticket/close/{id}', 'TicketController@close')->name('guzbyte.ticket.close');
        Route::get('ticket/edit/{id}', 'TicketController@edit')->name('guzbyte.ticket.edit')->middleware("user_agent_access");
        Route::patch('ticket/edit/{id}', 'TicketController@update')->name('guzbyte.ticket.update')->middleware("user_agent_access");
        Route::get('ticket/open', 'TicketController@opened')->name('guzbyte.ticket.opened');
        Route::get('ticket/close', 'TicketController@closed')->name('guzbyte.ticket.closed');
    });
});
//Ticket Agents
Route::group(['namespace' => 'Guzbyte\Ticket\Http\Controllers\TicketAgent'], function(){
    Route::group(['middleware' => ['installer', 'web', 'auth', 'is_ticket_agent', 'isActiveAgent']], function(){
        Route::get('ticket/agent', 'TicketAgentController@index')->name('guzbyte.agent.ticket.index');
        Route::get('ticket/agent/show/{id}/{slug}', 'TicketAgentController@show')->name('guzbyte.ticket.agent.show')->middleware(["is_agent"]);
        Route::post('ticket/agent/reply/{id}', 'TicketAgentController@reply')->name('guzbyte.ticket.agent.reply');
        Route::get('ticket/agent/close/{id}', 'TicketAgentController@close')->name('guzbyte.ticket.agent.close');
        Route::get('ticket/agent/edit/{id}', 'TicketAgentController@edit')->name('guzbyte.ticket.agent.edit')->middleware(["user_agent_access"]);
        Route::patch('ticket/agent/update/{id}', 'TicketAgentController@update')->name('guzbyte.ticket.agent.update')->middleware(["user_agent_access"]);
        Route::get('ticket/agent/open', 'TicketAgentController@opened')->name('guzbyte.agent.ticket.opened');
        Route::get('ticket/agent/close', 'TicketAgentController@closed')->name('guzbyte.agent.ticket.closed');
    });
});

//Ticket Manager
Route::group(['namespace' => 'Guzbyte\Ticket\Http\Controllers\TicketAdmin'], function(){
    Route::group(['middleware' => ['installer', 'web', 'auth', 'is_ticket_super_admin']], function(){
        //Category
        Route::get('ticket/admin/category', 'CategoryController@index')->name('guzbyte.admin.ticket.category');
        Route::get('ticket/admin/create', 'CategoryController@create')->name('guzbyte.admin.ticket.category.create');
        Route::post('ticket/admin/store', 'CategoryController@store')->name('guzbyte.admin.ticket.category.store');
        Route::get('ticket/admin/edit/{id}', 'CategoryController@edit')->name('guzbyte.admin.ticket.category.edit');
        Route::patch('ticket/admin/update/{id}', 'CategoryController@update')->name('guzbyte.admin.ticket.category.update');
        Route::delete('ticket/admin/delete/{id}', 'CategoryController@destroy')->name('guzbyte.admin.ticket.category.delete');

        //Priority
        Route::get('ticket/admin/priority', 'PriorityController@index')->name("guzbyte.admin.ticket.prioriy.index");
        Route::get('ticket/admin/priority/create', 'PriorityController@create')->name("guzbyte.admin.ticket.prioriy.create");
        Route::post('ticket/admin/priority/store', 'PriorityController@store')->name("guzbyte.admin.ticket.prioriy.store");
        Route::get('ticket/admin/priority/edit/{id}', 'PriorityController@edit')->name('guzbyte.admin.ticket.priority.edit');
        Route::patch('ticket/admin/priority/update/{id}', 'PriorityController@update')->name('guzbyte.admin.ticket.prioriy.update');
        Route::get('ticket/admin/priority/delete/{id}', 'PriorityController@destroy')->name('guzbyte.admin.ticket.priority.delete');

        //Status
        // Route::get('ticket/admin/status', 'StatusController@index')->name("guzbyte.admin.ticket.status.index");
        // Route::get('ticket/admin/status/create', 'StatusController@create')->name("guzbyte.admin.ticket.status.create");
        // Route::post('ticket/admin/status/store', 'StatusController@store')->name("guzbyte.admin.ticket.status.store");
        // Route::get('ticket/admin/status/edit/{id}', 'StatusController@edit')->name('guzbyte.admin.ticket.status.edit');
        // Route::patch('ticket/admin/status/update/{id}', 'StatusController@update')->name('guzbyte.admin.ticket.status.update');
        // Route::get('ticket/admin/status/delete/{id}', 'StatusController@destroy')->name('guzbyte.admin.ticket.status.delete');

        //Agents
        Route::get('ticket/admin/agents', 'AgentController@index')->name("guzbyte.admin.ticket.agent.index");
        Route::get('ticket/admin/agents/create', 'AgentController@create')->name("guzbyte.admin.ticket.agent.create");
        Route::post('ticket/admin/agents/store', 'AgentController@store')->name("guzbyte.admin.ticket.agent.store");
        Route::get('ticket/admin/agents/edit/{id}', 'AgentController@edit')->name("guzbyte.admin.ticket.agent.edit");
        Route::patch('ticket/admin/agents/update/{id}', 'AgentController@update')->name("guzbyte.admin.ticket.agent.update");
        Route::get('ticket/admin/agents/deactivate/{id}', 'AgentController@deactivate')->name("guzbyte.admin.ticket.agent.deactivate");
        Route::get('ticket/admin/agents/activate/{id}', 'AgentController@activate')->name("guzbyte.admin.ticket.agent.activate");
        Route::get('ticket/admin/agents/ticket/{id}', 'AgentController@getAgentTicket')->name("guzbyte.admin.agent.ticket.all");

        //Ticket Starts
        Route::get('ticket/admin', 'AdminTicketController@index')->name('guzbyte.admin.ticket.index');
        Route::post('ticket/admin/reply/{id}', 'AdminTicketController@reply')->name('guzbyte.ticket.admin.reply');
        Route::get('ticket/admin/ticket/show/{id}/{slug}', 'AdminTicketController@showTicket')->name("guzbyte.admin.agent.ticket.show");
        Route::get('ticket/admin/agent/close/{id}', 'AdminTicketController@closeTicket')->name('guzbyte.ticket.admin.close.ticket');
        Route::get('ticket/admin/ticket/edit/{id}', 'AdminTicketController@edit')->name('guzbyte.admin.ticket.edit');
        Route::patch('ticket/admin/ticket/update/{id}', 'AdminTicketController@update')->name('guzbyte.admin.ticket.update');
        Route::get('ticket/admin/assign-agent/{agent_id}', 'AdminTicketController@assign')->name('guzbyte.admin.ticket.assign');
        Route::patch('ticket/admin/assign-agent/{agent_id}', 'AdminTicketController@updateTicketAgent')->name('guzbyte.admin.ticket.update.agent.ticket');

        //
        Route::get("ticket/admin/ticket/open", 'AdminTicketController@openedTicket')->name("guzbyte.admin.ticket.opened");
        Route::get("ticket/admin/ticket/close", 'AdminTicketController@closedTicket')->name("guzbyte.admin.ticket.closed");
        Route::get("ticket/admin/ticket/all", 'AdminTicketController@allTicket')->name("guzbyte.admin.ticket.all");
        Route::get("ticket/admin/ticket/new", 'AdminTicketController@new')->name("guzbyte.admin.ticket.new");
    });
});


