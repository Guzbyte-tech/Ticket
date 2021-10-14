@extends("ticket::layouts.app")
@section('content')
<div class="container-fluid">

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5>Edit Ticket: {{ $ticket->title }}</h5></div>
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
                <form action="{{ route("guzbyte.ticket.update", [$ticket->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("PATCH")
                    <div class="form-group">
                        <input type="text" name="name" id="" class="form-control @error("name") is-invalid @enderror" placeholder="name" value="{{ $ticket->name }}">
                        @error("name")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" id="" class="form-control @error("email") is-invalid @enderror" placeholder="Email" value="{{ $ticket->email }}">
                        @error("email")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="text" name="title" id="" class="form-control @error("title") is-invalid @enderror" placeholder="Title" value="{{ $ticket->title }}">
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
                                <option value="{{ $category->id }}"
                                    @if ($ticket->category_id == $category->id)
                                        selected
                                    @endif
                                    >{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error("title")
                          <span class="invalid-feedback">
                              {{ $message }}
                          </span>
                        @enderror
                    </div>

                   <div class="form-group">
                       <textarea name="message" id="summernote" cols="30" rows="10" class="form-control @error("message") is-invalid @enderror">{{ $ticket->message }}</textarea>
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

                    @if (count(json_decode($ticket->attachment, true)) > 0)
                        @for ($i = 0; $i < count(json_decode($ticket->attachment, true)); $i++)
                            <a href="{{ asset("guz_ticket/attachment/".json_decode($ticket->attachment, true)[$i]) }}" target="_blank">{{ json_decode($ticket->attachment, true)[$i] }}</a> &nbsp;&nbsp;
                        @endfor                        
                    @endif
                    
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