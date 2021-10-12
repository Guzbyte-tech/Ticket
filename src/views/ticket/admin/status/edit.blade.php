@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h4>Edit {{ Str::ucfirst($status->name) }} Status</h4></div>
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
                    <form action="{{ route("guzbyte.admin.ticket.status.update", [$status->id]) }}" method="POST">
                        @csrf
                        @method("PATCH")
                        <div class="form-group">
                            <input type="text" name="name" class="form-control @error('name')
                                in_invalid @enderror" placeholder="Status Name" value="{{ $status->name }}">
                            @error("name")
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>
          
                        <div class="form-group">
                          <button class="btn btn-primary" type="submit">Submit</button>
                            
                      </div>
                    </form>
                </div>
              </div>
        </div>
    </div>
    


  </div><!-- /.container-fluid -->
@endsection