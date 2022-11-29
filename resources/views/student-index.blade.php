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
            name: 'Logo',
            sort: false,
            formatter: (function (cell) {
                return gridjs.html('<img src="/assets/images/logo-dark-sm.png" alt="" class="avatar-sm rounded-circle me-2" />');
            }),
            width: '50px',
        },
        {
            name: 'Username',
            width: '100px',
        },
        {
            name: 'Nama',
        },
        {
            name: 'Jenis Kelamin',
        },

        {
            name: 'Kelas',
            width: '200px',
        },
        {
            name: "Action",
            sort: false,
            width: '50px',
            formatter: (function (cell) {
                return gridjs.html(`
                    <div class="d-flex gap-3">
                        <a href="/student/${cell}/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" class="text-success">
                            <i class="mdi mdi-pencil font-size-18"></i>
                        </a>
                        <form action="/student/${cell}" method="POST" onsubmit="return confirm('Apakah anda yakin? Data yang sudah dihapus tidak dapat dikembalikan!')">
                            @csrf
                            @method('DELETE')
                            <button  data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus" class="text-danger" type="submit" style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;"><i class="mdi mdi-delete font-size-18""></i></button>
                        </form>
                    </div>`);
            })
      }
    ],
  pagination: {
    limit: 7
  },
  sort: true,
  search: true,
  data: [
    @foreach($users as $user)
    ["", "{{ $user->username }}", "{{ $user->name }}", "{{ $user->gender == 'MALE' ? 'L' : 'P' }}", "{{ $user->grade->name }}", "{{ $user->id }}"],
    @endforeach
  ]
}).render(document.getElementById("table-data"));

document.getElementById("select-grade").addEventListener('change', function () {
    location.href = "/grade/" + this.value + "/student"
})

</script>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row"> 
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="select-grade" aria-label="Kelas">
                                @foreach ($grades as $grade)
                                <option value="{{$grade->id}}" {{$selectedGrade == $grade->id ? 'selected' : ''}}>{{$grade['name']}}</option>                                
                                @endforeach
                            </select>
                            <label for="select-grade">Kelas</label>
                        </div>    
                    </div>
                </div>
                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm-auto">
                                
                            </div>
                            <div class="col-sm">
                                <div class="mt-3 mt-md-0 mb-3">
                                    <a href="/student/create" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i class="mdi mdi-plus me-1"></i> Tambah Siswa</a>
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