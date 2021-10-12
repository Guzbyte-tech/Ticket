@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h6>Priorities Levels</h6></div>
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
              <table class="table">
                <thead>
                  <tr>
                    <td class="text-center">S/N</td>
                    <td class="text-center">Name</td>
                    <td class="text-center">Color</td>
                    <td class="text-center">Action</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach($priorities as $priority)
                    <tr>
                      <td class="text-center">{{ $count++ }}</td>
                      <td class="text-center">{{ $priority->name }}</td>
                      <td class="d-flex justify-content-center"> <span  class="d-block" style="width: 20px; height: 20px; border: 1px solid {{ $priority->color }}; background: {{ $priority->color }}; border-radius: 10px;"></span> </td>
                      <td class="text-center">
                        <div class="dropdown dropright">
                          <a href="#" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-list"></i>
                          </a>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                              <a href="{{ route("guzbyte.admin.ticket.priority.edit", [$priority->id]) }}" class="dropdown-item"><i class="fa fa-edit" ></i> Edit</a>

                              <a href="{{ route("guzbyte.admin.ticket.priority.delete", [$priority->id]) }}" class="dropdown-item"><i class="fa fa-trash"></i> delete</a>
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
      
    </div>
    <!-- /.row -->

    


  </div><!-- /.container-fluid -->
@endsection