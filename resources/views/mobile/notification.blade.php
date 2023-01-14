@extends('mobile.layouts.master')

@section('content')
    @include('mobile.layouts.navbar')
    @foreach ($notifications as $notification)
        <div class="px-3 py-3">
            <p class="m-0 p-0"><strong>{{ $notification->title }}</strong></p>
            <p class="m-0 p-0"><small>{{ $notification->message }}</small></p>
        </div>
        <hr class="border opacity-50 mx-3">
    @endforeach
@endsection
