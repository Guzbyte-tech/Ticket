@extends("ticket::layouts.agents.app")
@section('ticketAgentCount')
@if ($ticketAgentCount > 0)
  <span class='badge badge-info right'>{{ $ticketAgentCount?? 0 }}</span>
@endif
@endsection
@section('content')
<div class="container-fluid">

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5>Title: {{ $ticket->title }}</h5></div>
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

                <div class="row">
                    <div class="col-lg-10">
                        <p>Name: {{ $name }}</p>
                        <p>Email: {{ $email }}</p>
                        <p>Status: 
                            @if ($ticket->status_id == 1)
                                <span class="badge badge-success">Open</span> 
                            @else
                                <span class="badge badge-danger">Close</span> 
                            @endif
                        
                        </p>
                        <p>Priority: 
                            @if (!is_null($ticket->priority_id))
                                <span class="badge" style="background-color: {{ \Guzbyte\Ticket\Models\TicketPriority::find($ticket->priority_id)->color }}">{{ \Guzbyte\Ticket\Models\TicketPriority::find($ticket->priority_id)->name }}</span> 
                            @endif

                        </p>
                        <p class="font-weight-bold"> <i>Your Message:</i></p>
                        {!! $ticket->message !!}
                        @if (count(json_decode($ticket->attachment, true)) > 0)
                            <p class="font-weight-bold">Attachment</p>
                            @for ($i = 0; $i < count(json_decode($ticket->attachment, true)); $i++)
                                <a href="{{ asset('guz_ticket/attachment/'.json_decode($ticket->attachment, true)[$i]) }}" target="_blank">{{ json_decode($ticket->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                            @endfor
                        @endif
                     
                        @if (auth()->user()->id == $ticket->user_id)
                            <a href="{{ route("guzbyte.ticket.edit", [$ticket->id]) }}" class="btn btn-outline-primary">Edit ticket</a>
                        @endif

                        <hr>
                        @foreach ($comments as $comment)
                            @if ($comment->sender == 0)
                                <div class="d-block mb-3 p-3 " style="width: 90%;  float: left; border-radius: 10px; background-color: #f2f2f2;">
                                    {!! $comment->message !!}
                                     
                                     @if (count(json_decode($comment->attachment, true)) > 0)
                                     <span class="font-weight-bold">Attachment</span> <br>
                                     @for ($i = 0; $i < count(json_decode($comment->attachment, true)); $i++)
                                         <a href="{{ asset('guz_ticket/attachment/'.json_decode($comment->attachment, true)[$i]) }}" target="_blank">{{ json_decode($comment->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                                     @endfor
                                 @endif
                                 <br>
                                     <small><i> {{ date("M d, Y h:i:s a", strtotime($ticket->created_at)) }}</i></small>
                                 </div>
                            
                            @else
                                <div class="mb-3 p-3"  style="width: 90%; float: right; border-radius: 10px; background-color: #dbf3c6; color: #5c6356; text-align:right">
                                    {!! $comment->message !!}
                                    @if (count(json_decode($comment->attachment, true)) > 0)
                                        <span class="font-weight-bold">Attachment</span> <br>
                                        @for ($i = 0; $i < count(json_decode($comment->attachment, true)); $i++)
                                            <a href="{{ asset('guz_ticket/attachment/'.json_decode($comment->attachment, true)[$i]) }}" target="_blank">{{ json_decode($comment->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                                        @endfor
                                    @endif
                                    <br>
                                    <small class="float-right"><i> {{ date("M d, Y h:i:s a", strtotime($ticket->created_at)) }}</i></small>
                                </div>
                                
                            @endif
                        @endforeach
                        

                        
                    </div>
                </div>
                @if ($ticket->status_id == 1)
                <div class="row">
                    <div class="col-g-10">
                        <hr>
                        <form action="{{ route("guzbyte.ticket.agent.reply", [$id]) }}" enctype="multipart/form-data" method="POST">
                            @csrf
                            <div class="form-group">
                                <textarea name="message" class="form-control" id="summernote" ></textarea>
                            </div>

                            <div class="form-group">
                                <label for="priority">Update ticket priority</label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="">Select Priority</option>
                                    @foreach ($priority as $priority)
                                        <option value="{{ $priority->id }}" >{{ $priority->name }}</option> 
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Select Attacment</label>
                                <input type="file" name="attachment[]" class="form-control-file" multiple>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
                @endif
                    
            </div>
          </div>
    </div>

  </div><!-- /.container-fluid -->
@endsection