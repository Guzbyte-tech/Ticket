

@extends("ticket::layouts.app")
@section('ticketCount')
@if ($ticketCount > 0)
<span class='badge badge-info right'>{{ $ticketCount?? 0 }}</span>
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
                        <p>Name: {{ auth()->user()->name }}</p>
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
                        <br>
                        @if ($ticket->status_id == 1)
                            @if (auth()->user()->id == $ticket->user_id)
                                <a href="{{ route("guzbyte.ticket.edit", [$ticket->id]) }}" class="btn btn-outline-primary">Edit ticket</a>
                            @endif
                        @endif

                        <hr>
                        <h5>Replies</h5>
                        @if (count($comments) > 0)
                            @foreach ($comments as $comment)
                                @if ($comment->sender == 1)
                                    <div class="d-block mb-3 p-3 " style="width: 90%; float: left; border-radius: 10px; background-color: #f2f2f2;">
                                        <div>
                                            {!! $comment->message !!}
                                            @if (count(json_decode($comment->attachment, true)) > 0)
                                            <span class="font-weight-bold">Attachment</span> <br>
                                            @for ($i = 0; $i < count(json_decode($comment->attachment, true)); $i++)
                                                <a href="{{ asset('guz_ticket/attachment/'.json_decode($comment->attachment, true)[$i]) }}" target="_blank">{{ json_decode($comment->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                                            @endfor
                                        @endif
                                        </div>
                                        @if (is_null($ticket->agent_id))
                                        <small class="d-block w-100 mt-1">
                                            {{ $adminId }} <br>
                                            <i> {{ date("M d, Y h:i:s a", strtotime($ticket->created_at)) }}</i>
                                        </small>
                                        @else
                                        <small class="d-block w-100 mt-1">
                                            {{ config('ticket.user')->find(Guzbyte\Ticket\Models\TicketAgent::find($ticket->agent_id)->user_id)->name }} <br>
                                            <i> {{ date("M d, Y h:i:s a", strtotime($ticket->created_at)) }}</i>
                                        </small>    
                                        @endif
                                        
                                    </div>
                                @else

                                    <div class="d-block float-right mb-3 p-3 "  style="width: 90%; border-radius: 10px; background-color: #dbf3c6; color: #5c6356; position: relative;">
                                        <div class="mb-2">
                                            {!! $comment->message !!}
                                        </div>
                                        @if (count(json_decode($comment->attachment, true)) > 0)
                                            <span class="font-weight-bold">Attachment</span> <br>
                                            @for ($i = 0; $i < count(json_decode($comment->attachment, true)); $i++)
                                                <a href="{{ asset('guz_ticket/attachment/'.json_decode($comment->attachment, true)[$i]) }}" target="_blank">{{ json_decode($comment->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                                            @endfor
                                        @endif
                                        <small class="float-right"><i> {{ date("M d, Y h:i:s a", strtotime($ticket->created_at)) }}</i></small>
                                    </div>
                                    
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                @if ($ticket->status_id == 1)
                    @if (count($comments) > 0)
                    <div class="row">
                        <div class="col-g-10">
                            <hr>
                            <form action="{{ route("guzbyte.ticket.reply") }}" enctype="multipart/form-data" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <div class="form-group">
                                    <textarea name="message" class="form-control" id="summernote" ></textarea>
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
                @endif
                
                    
            </div>
          </div>
    </div>


  </div><!-- /.container-fluid -->
@endsection