@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">

    <div class="card">
      <div class="card-header"><h4>All Ticket Raised</h4></div>
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
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>S/N</th>
                <th>User</th>
                <th>Agent Assigned</th>
                <th>Title</th>
                <th>Attachment</th>
                <th>Status</th>
                <th>Messages</th>
                <th>Priority</th>
                <th>Created at</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($tickets as $ticket)
                <tr>
                  <td>{{ $count++ }}</td>
                  <td>{{ $ticket->detail["user"] }}</td>
                  <td>@if (!is_null($ticket->agent_id))
                    {{ $ticket->detail["agent_name"] }}
                  @endif</td>
                  <td>
                    <a href="{{ route("guzbyte.admin.agent.ticket.show", [$ticket->id, $ticket->slug]) }}" style="text-decoration: none" class="d-inline-block text-dark @if ($ticket->admin_read == 0) font-weight-bold @endif">
                      {{ $ticket->title }}&nbsp;@if ($ticket->admin_read == 0)
                        <span class="badge badge-success">New</span>
                      @endif
                    </a>
                    
                    </td>
                  <td>
                    @if (count(json_decode($ticket->attachment, true)) > 0)
                      @for ($i = 0; $i < count(json_decode($ticket->attachment, true)); $i++)
                        <a href="{{ asset("guz_ticket/attachment/".json_decode($ticket->attachment, true)[$i]) }}" target="_blank">{{ json_decode($ticket->attachment, true)[$i] }}</a><br>
                      @endfor
                    @else
                      NA
                    @endif
                  </td>
                  <td>@if ($ticket->status_id == 1)
                    <span class="badge badge-success">open</span>
                    @else
                    <span class="badge badge-danger">close</span>
                  @endif</td>
                  <td>
                    <span class="d-inline-block mr-2" style="font-size: 20px;" title="Comments">
                      <a href="{{ route("guzbyte.admin.agent.ticket.show", [$ticket->id, $ticket->slug]) }}" style="text-decoration: none" class="d-inline-block text-secondary">
                      <i class="fa fa-comments"></i><sup class="d-inline-table" style="color:green;"> <b>
                        @if ($ticket->detail["unread"] > 0)
                          {{ $ticket->detail["unread"] }}
                        @endif
                      </b></sup>
                    </a>
                    </span>
                  </td>
                  <td>
                    @if (!is_null($ticket->priority_id))
                      <span class="badge" style="background-color: {{ Guzbyte\Ticket\Models\TicketPriority::find($ticket->priority_id)->color }}">{{ Guzbyte\Ticket\Models\TicketPriority::find($ticket->priority_id)->name }}</span>
                    @else
                      <span class="badge badge-secondary">None</span>
                    @endif
                  </td>
                  
                  <td>{{ date("F d, Y", strtotime($ticket->created_at)) }}</td>
                  {{-- <td class="text-center">
                    @if ($ticket->status_id == 1)
                      <span class="d-inline-block mr-2" style="font-size: 20px;" title="Close Ticket">
                        <a href="{{ route("guzbyte.ticket.close", [$ticket->id]) }}" style="text-decoration: none" class="d-inline-block text-danger">
                        <i class="fa fa-times"></i>
                      </a>
                      </span>
                    @endif
                  </td> --}}

                  <td class="text-center">
                    <div class="dropdown dropright">
                      
                        <a href="#" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-list"></i>
                        </a>
                      
                      
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <a href="{{ route("guzbyte.admin.agent.ticket.show", [$ticket->id, $ticket->slug]) }}" class="dropdown-item" type="button"><i class="fa fa-eye"></i> View ticket</a>
                            @if ($ticket->status_id == 1)
                              <a href="{{ route("guzbyte.admin.ticket.edit", [$ticket->id]) }}" class="dropdown-item" type="button"><i class="fa fa-edit"></i> Edit ticket</a>
                              <a href="{{ route("guzbyte.ticket.close", [$ticket->id]) }}" class="dropdown-item" type="button"><i class="fa fa-times"></i> Close ticket</a>
                              <a href="{{ route("guzbyte.admin.ticket.assign", [$ticket->id]) }}" class="dropdown-item" type="button"><i class="fa fa-toggle-on"></i> Assign to another agent</a>
                            @endif
                        </div>
                    </div>
                </td>
                </tr>
              @endforeach

              {{ $tickets->links() }}
              
            </tbody>
          </table>
        </div>
      </div>
    </div>


  </div><!-- /.container-fluid -->
@endsection