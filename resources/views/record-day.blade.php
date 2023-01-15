@extends('layouts.master')

@section('style')
    <!-- gridjs css -->
    <link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">
@endsection

@section('script')
    <!-- gridjs js -->
    <script src="/assets/libs/gridjs/gridjs.umd.js"></script>

    <!-- datepicker js -->
    <script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

    <script>
        // Basic Table
        new gridjs.Grid({
            columns: [{
                    name: 'Logo',
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
                    width: '100px',
                },
                {
                    name: 'Status',
                    formatter: function(cell) {
                        if (cell == 'SICK') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-warning font-size-12">SAKIT</span>
                            `);
                        } else if (cell == 'LEAVE') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-warning font-size-12">IZIN</span>
                            `);
                        } else if (cell == '0') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-success font-size-12">HADIR</span>
                            `);
                        } else {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-danger font-size-12">BELUM HADIR</span>
                            `);
                        }
                    }
                },
                {
                    name: 'Keterangan',
                    formatter: function(cell) {
                        if (cell == 'ON_TIME') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-success font-size-12">TEPAT WAKTU</span>
                            `);
                        } else if (cell == 'LATE') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-danger font-size-12">TERLAMBAT</span>
                            `);
                        } else if (cell == 'WAITING') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-warning font-size-12">MENUNGGU</span>
                            `);
                        } else if (cell == 'ACCEPT') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-success font-size-12">DITERIMA</span>
                            `);
                        } else if (cell == 'REJECT') {
                            return gridjs.html(`
                                <span class="badge badge-pill badge-soft-danger font-size-12">DITOLAK</span>
                            `);
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    name: 'Jam Masuk',
                },
                {
                    name: 'Jam Pulang',
                },
                {
                    name: ". . .",
                    width: '100px',
                    formatter: (function(cell) {
                        var ret = `
                            <div class="d-flex gap-1">
                                <a href="/record/user/${cell[0]}/{{ $selectedDay }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail" class="text-primary">
                                    <i class="mdi mdi-eye font-size-18"></i>
                                </a>
                        `

                        if (cell[3] == "") {
                            ret += `
                                <a href="/record/user/${cell[0]}/{{ $selectedDay }}/leave" data-bs-toggle="tooltip" data-bs-placement="top" title="Izin" class="text-primary">
                                    <i class="mdi mdi-file-document-edit font-size-18"></i>
                                </a>
                            `
                        }

                        if ("{{ date('Y-m-d') }}" == "{{ $selectedDay }}") {
                            if (cell[1] == "") {
                                ret += `
                                    <a  href="/record/user/${cell[0]}/clock-in" 
                                        type="button" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Absen Masuk" 
                                        class="text-success" 
                                        style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;">
                                    <i class="mdi mdi-login font-size-18"></i>
                                    </a>
                                `
                            }

                            if (cell[2] == "") {
                                ret += `
                                    <a href="/record/user/${cell[0]}/clock-out" 
                                        type="button" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="Absen Pulang" 
                                        class="text-danger" 
                                        style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;">
                                    <i class="mdi mdi-logout font-size-18"></i>
                                    </a>
                                `
                                //ret += `
                            //<form action="/record/user/${cell[0]}/clock-out" method="POST" onsubmit="return confirm('Absen pulang akan dikirim?')">
                            //    @csrf
                            //    <button  data-bs-toggle="tooltip" data-bs-placement="top" title="Absen Pulang" class="text-danger" type="submit" style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;">
                            //        <i class="mdi mdi-logout font-size-18"></i>
                            //    </button>
                            //</form>
                            //`
                            }
                        }

                        ret += `
                            </div>
                        `

                        return gridjs.html(ret)
                    })
                },
            ],
            sort: true,
            search: true,
            data: [
                @foreach ($users as $user)
                    ["{{ $user->image }}", "{{ $user->username }}", "{{ $user->name }}",
                        "{{ $user->gender == 'MALE' ? 'L' : 'P' }}",
                        "{{ count($user->records) > 0 ? ($user->records[0]->is_leave ? $user->records[0]->leave->type : '0') : '' }}",
                        "{{ count($user->records) > 0 ? ($user->records[0]->is_leave ? $user->records[0]->leave->leave_status : $user->records[0]->attend->clock_in_status) : '' }}",
                        "{{ count($user->records) > 0 && $user->records[0]->attend ? ($user->records[0]->attend->clock_in_time ? $user->records[0]->attend->clock_in_time : '-') : '-' }}",
                        "{{ count($user->records) > 0 && $user->records[0]->attend ? ($user->records[0]->attend->clock_out_time ? $user->records[0]->attend->clock_out_time : '-') : '-' }}",
                        [
                            "{{ $user->id }}",
                            "{{ count($user->records) == 0 ? null : ($user->records[0]->attend == null ? null : $user->records[0]->attend->clock_in_time) }}",
                            "{{ count($user->records) == 0 ? null : ($user->records[0]->attend == null ? null : $user->records[0]->attend->clock_out_time) }}",
                            "{{ count($user->records) == 0 ? null : ($user->records[0]->leave == null ? null : $user->records[0]->leave->type) }}",
                        ]
                    ],
                @endforeach
            ],
            pagination: {
                enabled: false,
                limit: 50,
                summary: false
            }
        }).render(document.getElementById("table-data"));

        document.getElementById("select-grade").addEventListener('change', function() {
            location.href = "/record/day?grade=" + this.value + "&day={{ $selectedDay }}"
        })

        flatpickr("#datepicker-grade", {
            onChange: function(selectedDates, dateStr, instance) {
                location.href = "/record/day?grade={{ $selectedGrade }}&day=" + dateStr
            },
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
                                <input type="text" class="form-control flatpickr-input" id="datepicker-grade"
                                    readonly="readonly" value="{{ $selectedDay }}">
                                <label for="select-grade">Periode</label>
                            </div>
                        </div>
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
