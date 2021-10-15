@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h4>Agent List</h4></div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>	
                                <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>	
                                {{ $error }}
                        </div>
                        @endforeach
                    @endif
                   <div class="table-responsive">
                       <table class="table">
                           <thead>
                               <tr>
                                   <th class="text-center">s/n</th>
                                   <th class="text-center">Agent Name</th>
                                   <th class="text-center">Category</th>
                                   <th class="text-center">Action</th>
                               </tr>
                           </thead>
                           <tbody>
                               @foreach ($agents as $agent)
                                    <tr>
                                        <td class="text-center">{{ $count++ }}</td>
                                        <td class="text-center">{{ \App\User::find($agent->user_id)->name }}</td>
                                        <td class="text-center">{{ \Guzbyte\Ticket\Models\TicketCategory::find($agent->category)->name }}</td>
                                        <td class="text-center">
                                            <div class="dropdown dropright">
                                                <a href="#" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                    <a href="{{ route("guzbyte.admin.ticket.agent.edit", $agent->id) }}" class="dropdown-item"><i class="fa fa-edit" ></i> Edit</a>
                                                    <a href="{{ route("guzbyte.admin.agent.ticket.all", [$agent->id]) }}" class="dropdown-item"><i class="fa fa-edit" ></i> View tickets</a>
                                                    @if ($agent->is_active == 1)
                                                        <a href="{{ route("guzbyte.admin.ticket.agent.deactivate", [$agent->id]) }}" class="dropdown-item"><i class="fa fa-toggle-off"></i> De-activate</a>
                                                    @else
                                                        <a href="{{ route("guzbyte.admin.ticket.agent.activate", [$agent->id]) }}" class="dropdown-item"><i class="fa fa-toggle-on"></i> Activate</a>
                                                    @endif
                                                </div>
                                              </div>
                                        </td>
                                    </tr>
                               @endforeach
                           </tbody>
                       </table>
                   </div>
                </div>
              </div>
        </div>
    </div>
    


  </div><!-- /.container-fluid -->
@endsection