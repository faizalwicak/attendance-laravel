@extends('mobile.layouts.master')

@section('content')
    @include('mobile.layouts.navbar')
    @foreach ($users as $user)
        <div class="px-3 py-3">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <p class="m-0 p-0"><strong>{{ $user->name }}</strong></p>
                    <p class="m-0 p-0"><small>{{ $user->username }}</small></p>
                </div>
                <div class="col-auto">
                    <p class="m-0 p-0">
                        @if (count($user->records) == 0)
                            Belum Hadir
                        @elseif (!$user->records[0]->is_leave)
                            Hadir
                        @elseif($user->records[0]->leave->type == 'SICK')
                            Sakit
                        @elseif($user->records[0]->leave->type == 'LEAVE')
                            Izin
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <hr class="border opacity-50 mx-3">
    @endforeach
@endsection
