@extends('mobile.layouts.master')

@section('content')
    @include('mobile.layouts.navbar')
    @foreach ($records as $record)
        <div class="px-3 py-3">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <p class="m-0 p-0"><strong>{{ $record->leave->type == 'SICK' ? 'Sakit' : 'Izin' }}</strong></p>
                    <p class="m-0 p-0"><small>{{ date('d-m-Y', strtotime($record->date)) }}</small></p>
                </div>
                <div class="col-auto">
                    <p class="m-0 p-0">
                        {{ $record->leave->leave_status == 'WAITING' ? 'Menunggu konfirmasi' : ($record->leave->leave_status == 'ACCEPT' ? 'Diterima' : 'Ditolak') }}
                    </p>
                </div>
            </div>
        </div>
        <hr class="border opacity-50 mx-3">
    @endforeach
@endsection
