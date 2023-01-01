@extends('layouts.master')

@section('style')
    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">
@endsection

@section('script')
    <!-- datepicker js -->
    <script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
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
            @if ($record == null)
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
                {{-- 
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-6">
                            <label for="input-lat" class="form-label">Latitude</label>
                            <input name="lat" value="{{ $record->attend->clock_in_lat }}" type="number"
                                class="form-control" id="input-lat" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="input-lng" class="form-label">Longitude</label>
                            <input name="lng" value="{{ $record->attend->clock_in_lng }}" type="number"
                                class="form-control" id="input-lng" readonly>
                        </div>
                    </div>
                </div> --}}
            @endif

        </div>
        <!-- end card body -->
    </div>
@endsection
