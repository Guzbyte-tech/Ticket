@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header bg-white"><h6>Edit {{ Str::ucfirst($priority->name) }} Priority</h6></div>
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
          <form action="{{ route("guzbyte.admin.ticket.prioriy.update", [$priority->id]) }}" method="POST">
            @csrf
            @method("PATCH")
            <div class="form-group">
              <input type="text" name="name" class="form-control @error("name")
                is_invalid
              @enderror" placeholder="Priority Name" value="{{ $priority->name }}">
              @error("name")
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label for="">Select Color</label>
              <input type="color" name="color" id="" class="form-control col-2" value="{{ $priority->color }}">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">Submit</button>
            </div>
  
          </form>
          <!-- /.row -->
        </div>
      </div>
    </div>
    

    


  </div><!-- /.container-fluid -->
@endsection