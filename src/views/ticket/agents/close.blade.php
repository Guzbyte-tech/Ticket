@extends("ticket::layouts.agents.app")
@section('ticketAgentCount')
@if ($ticketAgentCount > 0)
  <span class='badge badge-info right'>{{ $ticketAgentCount?? 0 }}</span>
@endif
@endsection
@section('content')
<div class="container-fluid">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header"><h5>Closed tickets</h5></div>
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
                <table class="table" id="datatable">
                  <thead>
                    <tr>
                      <th>S/N</th>
                      <th>Title</th>
                      <th>Attachment</th>
                      <th>Status</th>
                      <th>Message</th>
                      <th>Priority</th>
                      <th>Created at</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($tickets as $ticket)
                    <tr>
                      <td>{{ $count++ }}</td>
                      <td>{{ $ticket->title }}</td>
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
                      @endif
                    </td>
                    <td>
                      <span class="d-inline-block mr-2" style="font-size: 20px;" title="Comments">
                        <a href="{{ route("guzbyte.ticket.agent.show", [$ticket->id, $ticket->slug]) }}" style="text-decoration: none" class="d-inline-block text-secondary">
                        <i class="fa fa-envelope"></i><sup class="d-inline-table" style="color:green"> <b>@if ($ticket->unread > 0)
                          {{ $ticket->unread }}
                        @endif</b></sup>
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
                      <td class="text-center">  
                        <div class="dropdown dropright">
                          <a href="{{ route("guzbyte.ticket.agent.show", [$ticket->id, $ticket->slug]) }}" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-list"></i>
                          </a>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                              <a href="{{ route("guzbyte.ticket.agent.show", [$ticket->id, $ticket->slug]) }}" class="dropdown-item"><i class="fa fa-eye" ></i> View ticket</a>
                              @if ($ticket->status_id == 1)
                                <a href="{{ route("guzbyte.ticket.agent.edit", [$ticket->id]) }}" class="dropdown-item" type="button"><i class="fa fa-edit"></i> Edit ticket</a>
                                <a href="{{ route("guzbyte.ticket.agent.close", [$ticket->id]) }}" class="dropdown-item"><i class="fa fa-trash"></i> Close Ticket</a>
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


  </div><!-- /.container-fluid -->
@endsection