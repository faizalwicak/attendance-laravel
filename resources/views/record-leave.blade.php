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
                        if (cell != "" && cell != null) {
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
                    name: 'Kelas',
                },
                {
                    name: 'Tipe',
                    formatter: function(cell) {
                        if (cell == 'SICK') {
                            return gridjs.html(`SAKIT`);

                        } else if (cell == 'LEAVE') {
                            return gridjs.html(`IZIN`);

                        } else {
                            return gridjs.html(`-`);
                        }
                    }
                },
                {
                    name: 'Status',
                    formatter: function(cell) {
                        if (cell == 'ACCEPT') {
                            return gridjs.html(`
                              <span class="badge badge-pill badge-soft-success font-size-12">DITERIMA</span>
                            `);

                        } else if (cell == 'REJECT') {
                            return gridjs.html(`
                              <span class="badge badge-pill badge-soft-danger font-size-12">DITOLAK</span>
                            `);

                        } else {
                            return gridjs.html(`
                              <span class="badge badge-pill badge-soft-warning font-size-12">MENUNGGU</span>
                            `);
                        }
                    }
                },
                {
                    name: '. . .',
                    sort: false,
                    // width: '50px',
                    formatter: (function(cell) {
                        return gridjs.html(`
                          <div class="d-flex gap-3">
                            <a href="/record/user/${cell}/{{ $selectedDay }}" type="button" class="btn btn-primary btn-sm btn-rounded">Detail</a>
                          </div>`);
                    })
                }
            ],
            sort: true,
            search: true,
            data: [
                @foreach ($leaves as $leave)
                    [
                        "{{ $leave->record->user->image }}",
                        "{{ $leave->record->user->username }}",
                        "{{ $leave->record->user->name }}",
                        "{{ $leave->record->user->gender == 'MALE' ? 'L' : 'P' }}",
                        "{{ $leave->record->user->grade->name }}", "{{ $leave->type }}",
                        "{{ $leave->leave_status }}",
                        "{{ $leave->record->user->id }}",

                    ],
                @endforeach
            ],
            pagination: {
                enabled: false,
                limit: 50,
                summary: false
            }
        }).render(document.getElementById("table-absent"));

        flatpickr("#datepicker-periode", {
            onChange: function(selectedDates, dateStr, instance) {
                location.href = "/record/leave?day=" + dateStr
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
                                <input type="text" class="form-control flatpickr-input" id="datepicker-periode"
                                    readonly="readonly" value="{{ $selectedDay }}">
                                <label for="datepicker-periode">Periode</label>
                            </div>

                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm-auto">

                                </div>
                                <div class="col-sm">

                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </div>

                    <div id="table-absent"></div>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
@endsection
