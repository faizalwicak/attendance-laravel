@extends('layouts.master')

@section('style')
    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">

    <!-- leaflet Css -->
    <link href="/assets/libs/leaflet/leaflet.css" rel="stylesheet" type="text/css" />
@endsection

@section('script')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $notification ? '/notification/' . $notification->id : '/notification' }}" method="POST">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf
                @if ($notification)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="input-title" class="form-label">Judul</label>
                    <input name="title" type="text" class="form-control" id="input-title"
                        value="{{ old('title', $notification ? $notification->title : '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="input-message" class="form-label">Pesan</label>
                    <textarea name="message" type="text" class="form-control" id="input-message" required>{{ old('message', $notification ? $notification->message : '') }}</textarea>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-md">Simpan</button>
                </div>
            </form>
        </div>
        <!-- end card body -->
    </div>
@endsection
