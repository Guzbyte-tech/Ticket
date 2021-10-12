@extends("ticket::layouts.app")
@section('content')
<div class="container-fluid">

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5>Raise a ticket</h5></div>
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
                <form action="{{ route("guzbyte.ticket.store") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" id="" class="form-control @error("name") is-invalid @enderror" placeholder="name" value="{{ auth()->user()->name }}">
                        @error("name")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" id="" class="form-control @error("email") is-invalid @enderror" placeholder="Email" value="{{ auth()->user()->email }}">
                        @error("email")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="text" name="title" id="" class="form-control @error("title") is-invalid @enderror" placeholder="Title">
                        @error("title")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="category" id="" class="form-control @error("category")
                            is-invalid
                        @enderror">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error("title")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                   <div class="form-group">
                       <textarea name="message" id="summernote" cols="30" rows="10" class="form-control @error("message") is-invalid @enderror"></textarea>
                       @error("message")
                           <div class="invalid-feedback">
                               {{ $message }}
                           </div>
                       @enderror
                   </div>

                   <div class="form-group">
                       <label for="attachement"></label>
                    <input type="file" name="attachment[]" id="attachment" class="form-control-file @error("attachment")
                        is-invalid
                    @enderror" multiple>
                    @error("attachment")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Submit</button>
                     </div>


                    
                </form>
            </div>
          </div>
    </div>


  </div><!-- /.container-fluid -->
@endsection