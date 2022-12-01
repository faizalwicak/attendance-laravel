@extends('layouts.master')

@section('style')

<!-- gridjs css -->
<link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

@endsection

@section('script')

<!-- gridjs js -->
<script src="/assets/libs/gridjs/gridjs.umd.js"></script>

<script>
// Basic Table
new gridjs.Grid({
  columns:
    [
        {
            name: 'Foto',
            sort: false,
            formatter: (function (cell) {
                if (cell) {
                    return gridjs.html(`<img src="/images/${cell}" alt="" class="avatar-sm rounded-circle me-2" />`);
                }
                return gridjs.html('<img src="/assets/images/default-user.png" alt="" class="avatar-sm rounded-circle me-2" />');
            }),
            width: '80px',
        },
        {
            name: 'Username',
        },
        {
            name: 'Nama',
        },
        @if (auth()->user()->role == 'SUPERADMIN')
        {
            name: 'Sekolah',
            width: '200px',
        },            
        @else
        {
            name: 'ROLE',
            width: '200px',
        },  
        @endif
        {
            name: "Action",
            sort: false,
            width: '100px',
            formatter: (function (cell) {
                var access = `<a href="/admin/${cell[0]}/access" data-bs-toggle="tooltip" data-bs-placement="top" title="Akses" class="text-success">
                    <i class="mdi mdi-key font-size-18"></i>
                </a>`
                return gridjs.html(`
                    <div class="d-flex gap-3  justify-content-end">
                        ${cell[1] == 'OPERATOR' ? access : ''}
                        
                        <a href="/admin/${cell[0]}/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" class="text-success">
                            <i class="mdi mdi-pencil font-size-18"></i>
                        </a>
                        <form action="/admin/${cell[0]}" method="POST" onsubmit="return confirm('Apakah anda yakin? Data yang sudah dihapus tidak dapat dikembalikan!')">
                            @csrf
                            @method('DELETE')
                            <button  data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus" class="text-danger" type="submit" style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;"><i class="mdi mdi-delete font-size-18""></i></button>
                        </form>
                    </div>`);
            })
      }
    ],
  pagination: {
    limit: 10
  },
  sort: true,
  search: true,
  data: [
    @foreach($users as $user)
    ["{{ $user->image }}", "{{ $user->username }}", "{{ $user->name }}", "{{ auth()->user()->role == 'SUPERADMIN' ? $user->school->name : $user->role }}", ["{{ $user->id }}", "{{ $user->role }}"]],
    @endforeach
  ]
}).render(document.getElementById("table-data"));


</script>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm-auto">
                                
                            </div>
                            <div class="col-sm">
                                <div class="mt-3 mt-md-0 mb-3">
                                    <a href="/admin/create" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i class="mdi mdi-plus me-1"></i> Tambah Admin</a>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>

                <div id="table-data"></div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection