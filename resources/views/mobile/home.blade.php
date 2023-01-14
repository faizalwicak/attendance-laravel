@extends('mobile.layouts.master')

@section('style')
    <style>
        .card-menu {
            height: 100px;
            color: inherit;
            text-decoration: inherit;
        }

        a:link {
            text-decoration: none;
        }

        a:visited {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        a:active {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <div class="row m-0 bg-primary text-white px-3 py-5 justify-content-between">
        <div class="col-auto">
            <p>
                <img src="/images/{{ auth()->user()->school->image }}" alt="{{ auth()->user()->school->name }}"
                    class="rounded-circle" height="50px" width="50px" />
                <span class="ps-2">{{ auth()->user()->school->name }}</span>
            </p>
            <h5>{{ auth()->user()->name }}</h5>
            <p>{{ auth()->user()->grade->name }}</p>
        </div>
        <div class="col-auto">
            <img src="/images/{{ auth()->user()->image }}" height="80px" width="80px" class="rounded-circle"
                alt="img">
        </div>
    </div>
    <div class="row px-3 pt-3 g-3">
        <div class="col-3">
            <a href="{{ route('mobile.clock') }}">
                <div class="card card-menu">
                    <div class="card-body text-center p-2">
                        <p class="card-text">
                            <img src="/assets/images/icon_location_tick.svg" alt="presensi" height="25px" width="25px" />
                            <br>
                            <small>Presensi</small>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('mobile.clock.history') }}">
                <div class="card card-menu">
                    <div class="card-body text-center p-2">
                        <p class="card-text">
                            <img src="/assets/images/icon_clipboard.svg" alt="presensi" height="25px" width="25px" />
                            <br>
                            <small>Riwayat Presensi</small>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('mobile.leave.create') }}">
                <div class="card card-menu">
                    <div class="card-body text-center p-2">
                        <p class="card-text">
                            <img src="/assets/images/icon_calendar.svg" alt="presensi" height="25px" width="25px" />
                            <br>
                            <small>Buat Izin</small>
                        </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('mobile.leave.list') }}">
                <div class="card card-menu">
                    <div class="card-body text-center p-2">
                        <p class="card-text">
                            <img src="/assets/images/icon_directbox_notif.svg" alt="presensi" height="25px"
                                width="25px" />
                            <br>
                            <small>Riwayat Izin</small>
                        </p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row px-3 pt-3">
        <div class="col">
            <div class="card py-3">
                <div class="row align-items-center g-0 justify-content-between">
                    <div class="col-auto">
                        <div class="card-body">
                            <p class="card-text">
                                @if ($quote != null)
                                    {{ $quote->message }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <img src="/assets/images/quote.png" class="img-fluid rounded-end pe-3" alt="quote">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row px-3 pt-3">
        <div class="col">
            <div class="card">
                <div class="row g-0 justify">
                    <div class="card-body">
                        <p><strong>Keterangan:</strong></p>
                        @if ($record == null)
                            Anda belum presensi.
                        @elseif ($record->is_leave && $record->leave->type == 'SICK')
                            Anda Izin Sakit hari ini.
                        @elseif ($record->is_leave && $record->leave->type == 'LEAVE')
                            Anda Izin hari ini.
                        @else
                            @if ($record->attend->clock_in_time != null)
                                @if ($record->attend->clock_in_status == 'ON_TIME')
                                    Anda masuk tepat waktu.
                                @else
                                    Anda terlambat masuk.
                                @endif
                            @endif

                            @if ($record->attend->clock_out_time != null)
                                Anda sudah presensi pulang.
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row px-3 pt-3 pb-5">
        <div class="col">
            <div class="card">
                <div class="row g-0 justify">
                    <div class="card-body">
                        <p><strong>Link Penting:</strong></p>
                        @foreach ($links as $link)
                            <p class="card-text">
                                - {{ $link->title }} (<a href="{{ $link->link }}" target="_blank"
                                    rel="noopener noreferrer">{{ $link->link }}</a>)
                            </p>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
