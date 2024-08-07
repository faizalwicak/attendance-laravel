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
            columns: [{
                name: 'Foto',
                sort: false,
                formatter: (function(cell) {
                    if (cell != "") {
                        return gridjs.html(
                            `<img src="/images/${cell}" alt="" class="avatar-sm rounded-circle me-2" />`
                        );
                    }
                    return gridjs.html(
                        '<img src="/assets/images/default-user.png" alt="" class="avatar-sm rounded-circle me-2" />'
                    );
                }),
                width: '80px',
            }, {
                name: 'Username',
                width: '100px',
            }, {
                name: 'Nama',
            }, {
                name: 'Jenis Kelamin',
            }, {
                name: 'Kelas',
                width: '200px',
            }, {
                name: 'Id Perangkat',
                width: '200px',
            }, {
                name: ". . .",
                sort: false,
                width: '110px',
                formatter: (function(cell) {
                    return gridjs.html(`
                    <div class="d-flex gap-3">
                        <form action="/student/${cell}/reset-device" method="POST" onsubmit="return confirm('Device user akan di reset!')">
                            @csrf
                            <button data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Device" class="text-warning" type="submit" style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;">
                                <i class="mdi mdi-devices font-size-18"></i>
                            </button>
                        </form>
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
            }],
            pagination: {
                limit: 10
            },
            sort: true,
            search: true,
            data: [
                @foreach ($users as $user)
                    ["{{ $user->image }}", "{{ $user->username }}", "{{ $user->name }}",
                        "{{ $user->gender == 'MALE' ? 'L' : 'P' }}", "{{ $user->grade->name }}",
                        "{{ $user->device_id }}",
                        "{{ $user->id }}",
                    ],
                @endforeach
            ]
        }).render(document.getElementById("table-data"));

        document.getElementById("select-grade").addEventListener('change', function() {
            location.href = "/student?grade=" + this.value
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
                                    <option value="" disabled selected>-- pilih kelas --</option>
                                    @foreach ($grades as $grade)
                                        <option value="{{ $grade->id }}"
                                            {{ $selectedGrade == $grade->id ? 'selected' : '' }}>{{ $grade['name'] }}
                                        </option>
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
                                    <div class="btn-group">
                                        <a href="/student/create"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-1"><i
                                                class="mdi mdi-plus me-1"></i> Tambah Siswa</a>
                                        <a href="/student/import"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-1"><i
                                                class="mdi mdi-import me-1"></i> Import Siswa</a>
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
