@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h6>Statuses Levels</h6></div>
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
                    <td class="text-center">Action</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach($statuses as $status)
                    <tr>
                      <td class="text-center">{{ $count++ }}</td>
                      <td class="text-center">{{ $status->name }}</td>
                      <td class="text-center">
                        <div class="dropdown dropright">
                          <a href="#" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-list"></i>
                          </a>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                              <a href="{{ route("guzbyte.admin.ticket.status.edit", [$status->id]) }}" class="dropdown-item"><i class="fa fa-edit" ></i> Edit</a>

                              <a href="{{ route("guzbyte.admin.ticket.status.delete", [$status->id]) }}" class="dropdown-item"><i class="fa fa-trash"></i> delete</a>
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