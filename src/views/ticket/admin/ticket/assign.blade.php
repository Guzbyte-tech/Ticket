@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5>Assign ticket: {{ $ticket->title }}</h5></div>
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

                <form action="{{ route("guzbyte.admin.ticket.update.agent.ticket", [$ticket->id]) }}" method="POST">
                    @csrf
                    @method("PATCH")
                    <div class="form-group">
                        <label for="agent">Select new Agent</label>
                        <select name="agent" id="agent" class="form-control">
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}">{{ App\User::find(Guzbyte\Ticket\Models\TicketAgent::find($agent->id)->user_id)->name }}</option>
                            @endforeach
                            
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
                
                
            </div>
          </div>
    </div>
@endsection