@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h4>Create Agent</h4></div>
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
                    <p class=""> To create an agent please make enter agents email address <br> <span class="text-danger font-weight-bold">Note: Please make sure user is already registered in you database.</span></p>
                    <form action="{{ route("guzbyte.admin.ticket.agent.store") }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="email" name="email" class="form-control @error('email')
                                in_invalid @enderror" placeholder="Agent Email">
                            @error("email")
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                           <select name="category" id="" class="form-control @error("category") is-invalid @enderror">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                           </select>
                            @error("category")
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