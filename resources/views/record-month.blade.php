@extends('layouts.master')

@section('style')
    <!-- gridjs css -->
    <link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" href="/assets/libs/flatpickr/plugins/monthSelect/style.css">

    <style>
        .item {
            color: black;
        }

        .item.late {
            color: red;
        }

        .item.ontime {
            color: green;
        }
    </style>
@endsection

@section('script')
    <!-- gridjs js -->
    <script src="/assets/libs/gridjs/gridjs.umd.js"></script>

    <!-- datepicker js -->
    <script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="/assets/libs/flatpickr/plugins/monthSelect/index.js"></script>

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
                    width: '100px',
                },
                {
                    name: 'Username',
                    width: '100px',
                },
                {
                    name: 'Nama',
                    width: '300px',
                },
                {
                    name: 'Jenis Kelamin',
                    width: '100px',
                },
                @foreach ($days as $d)
                    {
                        name: '{{ explode('-', $d)[2] }}',
                        width: '100px',
                        formatter: function(cell) {
                            if (cell[2].includes('TL')) {
                                return gridjs.html(`
                                    <a class="item late" href="/record/user/${cell[0]}/${cell[1]}">${cell[2]}</a>
                                `);
                            }

                            if (cell[2].includes('TW')) {
                                return gridjs.html(`
                                    <a class="item ontime" href="/record/user/${cell[0]}/${cell[1]}">${cell[2]}</a>
                                `);
                            }

                            return gridjs.html(`
                                <a class="item" href="/record/user/${cell[0]}/${cell[1]}">${cell[2]}</a>
                            `);
                        }
                    },
                @endforeach {
                    name: 'Total',
                    width: '100px',
                },
                {
                    name: 'Masuk',
                    width: '100px',
                },
                {
                    name: 'Tepat Waktu (TW)',
                    width: '100px',
                },
                {
                    name: 'Terlambat (TL)',
                    width: '100px',
                },
                {
                    name: 'Sakit',
                    width: '100px',
                },
                {
                    name: 'Izin',
                    width: '100px',
                },
                {
                    name: 'Alpa',
                    width: '100px',
                },


            ],
            sort: true,
            search: true,
            data: @json($userArray),
            pagination: {
                enabled: false,
                limit: 50,
                summary: false
            }
        }).render(document.getElementById("table-data"));

        document.getElementById("select-grade").addEventListener('change', function() {
            location.href = "/record/month?grade=" + this.value + "&month={{ $selectedMonth }}"
        })

        flatpickr("#datepicker-grade", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "Y F",
                })
            ],
            onChange: function(selectedDates, dateStr, instance) {
                location.href = "/record/month?grade={{ $selectedGrade }}&month=" + dateStr
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
                                    readonly="readonly" value="{{ $selectedMonth }}">
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
                        <div class="modal-button mt-2">
                            <div class="row align-items-start">
                                <div class="col-sm-auto">

                                </div>
                                <div class="col-sm">
                                    <div class="mt-3 mt-md-0 mb-3">
                                        <a href="/record/export?month={{ $selectedMonth }}&grade={{ $selectedGrade }}"
                                            class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i
                                                class="mdi mdi-export me-1"></i> Export</a>
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
