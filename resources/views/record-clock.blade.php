@extends('layouts.master')

@section('style')
    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">
@endsection

@section('script')
    <!-- datepicker js -->
    <script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

    <script>
        flatpickr("#input-time", {
            enableTime: !0,
            noCalendar: !0,
            dateFormat: "H:i",
            time_24hr: !0,
            hourIncrement: 1,
            minuteIncrement: 1
        })
    </script>
@endsection

@section('content')
    <form class="card" method="POST">
        @csrf
        <div class="card-body">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="input-name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="input-name" value="{{ $user->name }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="input-date" class="form-label">Tanggal</label>
                        <input type="text" class="form-control" id="input-date"
                            value="{{ date('d-m-Y', strtotime($selectedDay)) }}" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="mb-3">
                        <label for="input-time" class="form-label">Waktu</label>
                        <input type="text" class="form-control" id="input-time" name="time"
                            value="{{ date('H:i', strtotime($selectedTime)) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary" value="Simpan">
                    </div>
                </div>
            </div>
            {{-- @if ($record == null)
                @if ($selectedDay == date('Y-m-d'))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="input-status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="input-status" value="BELUM HADIR" readonly>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="input-status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="input-status" value="TIDAK HADIR (ALPA)"
                                    readonly>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif ($record->is_leave)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-name" class="form-label">Status</label>
                            <input name="name" type="text" class="form-control" id="input-name" value="IZIN"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Keterangan</label>
                            <input name="name" type="text" class="form-control" id="input-name"
                                value="{{ $record->leave->type == 'LEAVE' ? 'IZIN' : 'SAKIT' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="input-name" class="form-label">Status Izin</label>
                    <input name="name" type="text" class="form-control" id="input-name"
                        value="{{ $record->leave->leave_status == 'WAITING' ? 'MENUNGGU' : ($record->leave->leave_status == 'ACCEPT' ? 'DIRETIMA' : 'DITOLAK') }}"
                        readonly>
                </div>

                @if ($record->leave->file != null && $record->leave->file != '')
                    <div class="mb-3">
                        <img src="/images/leave/{{ $record->leave->file }}" alt="" class="img-fluid" />
                    </div>
                @endif

                <div class="d-flex">
                    <form method="POST" action="/record/leave/status">
                        @csrf
                        @method('put')
                        <input name="id" value="{{ $record->id }}" type="hidden" />
                        <input name="accept" value="1" type="hidden" />
                        <button type="submit" class="btn btn-success w-md"
                            @if ($record->leave->leave_status == 'ACCEPT') disabled @endif>Terima</button>
                    </form>
                    <form method="POST" action="/record/leave/status">
                        @csrf
                        @method('put')
                        <input name="id" value="{{ $record->id }}" type="hidden" />
                        <input name="accept" value="0" type="hidden" />
                        <button type="submit" class="btn btn-danger w-md ms-2"
                            @if ($record->leave->leave_status == 'REJECT') disabled @endif>Tolak</button>
                    </form>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-name" class="form-label">Status</label>
                            <input name="name" type="text" class="form-control" id="input-name" value="HADIR"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Keterangan</label>
                            <input name="name" type="text" class="form-control" id="input-name"
                                value="{{ $record->attend->clock_in_status == 'ON_TIME' ? 'ON TIME' : 'TERLAMBAT' }}"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockin" class="form-label">Jam Masuk</label>
                            <input name="clock_in" value="{{ $record->attend->clock_in_time }}" type="text"
                                class="form-control flatpickr-input active" id="input-clockin" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Jam Pulang</label>
                            <input name="clock_out" value="{{ $record->attend->clock_out_time }}" type="text"
                                class="form-control flatpickr-input active" id="input-clockout" readonly="readonly">
                        </div>
                    </div>
                </div>
            @endif --}}

        </div>
        <!-- end card body -->
    </form>
@endsection
