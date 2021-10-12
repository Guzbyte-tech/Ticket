@extends("ticket::layouts.admin.app")
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h4>Categories</h4></div>
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
                   <table class="table">
                        <thead>
                            <tr>
                                <td>S/N</td>
                                <td>Name</td>
                                <td class="text-center">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-center">
                                        <div class="dropdown dropright">
                                            <a href="#" class="dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                <a href="{{ route("guzbyte.admin.ticket.category.edit", [$category->id]) }}" class="dropdown-item"><i class="fa fa-edit" title="View Transaction"></i> Edit</a>

                                                <a href="#" class="dropdown-item"><i class="fa fa-trash" title="View Transaction"  onclick="event.preventDefault();
                                                    document.getElementById('delete-cat{{ $category->id }}').submit();"></i> delete</a>

                                                <form id="delete-cat{{ $category->id }}" action="{{ route("guzbyte.admin.ticket.category.delete", [$category->id]) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method("DELETE")
                                                </form>
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
    


  </div><!-- /.container-fluid -->
@endsection