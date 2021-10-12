@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h4>Create Category</h4></div>
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
                    <form action="{{ route("guzbyte.admin.ticket.category.store") }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="category_name" class="form-control @error('category_name')
                                in_invalid @enderror" placeholder="Category Name">
                            @error("category_name")
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