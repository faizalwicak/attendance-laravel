@extends('mobile.layouts.master')

@section('content')
    @include('mobile.layouts.navbar')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif

    <div class="row px-3">
        <div class="col-12">

            <div class="text-center pt-5">
                <img src="/images/{{ auth()->user()->image }}" height="100px" width="100px" class="rounded-circle"
                    alt="img">
                <form id="form-image" action="/mobile/profile/image" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input id="input-image" type="file" style="display: none;" name="image">
                </form>
                <div>
                    <button id="button-image" class="btn btn-outline-primary mt-3" type="button">Ganti Foto</button>
                </div>
            </div>
            <div class="text-center pt-3">
                <h5>{{ auth()->user()->name }}</h5>
            </div>
            <div class="text-center">
                <h6>{{ auth()->user()->username }}</h6>
            </div>
            <div class="form-group pt-3">
                <label for="input-school">Sekolah</label>
                <input id="input-school" class="form-control" type="text" value="{{ auth()->user()->school->name }}"
                    readonly>
            </div>
            <div class="form-group pt-3">
                <label for="input-grade">Kelas</label>
                <input id="input-grade" class="form-control" type="text" value="{{ auth()->user()->grade->name }}"
                    readonly>
            </div>

            <div class="pt-3">
                <a href="/mobile/profile/password" class="btn btn-outline-primary w-100" type="button">
                    Ganti Password
                </a>
            </div>
            <div class="pt-3">
                <a href="/logout" class="btn btn-outline-danger w-100" type="button">Keluar</a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById("input-image").onchange = function() {
            console.log('suubmit')
            document.getElementById("form-image").submit();
        };
        document.getElementById('button-image').onclick = function() {
            document.getElementById('input-image').click();
        };
    </script>
@endsection
