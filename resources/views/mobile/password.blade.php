@extends('mobile.layouts.master')

@section('content')
    @include('mobile.layouts.navbar')
    <div class="row px-3 py-3">

        <div class="col">
            <form action="/mobile/profile/password" method="POST">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf

                <div class="mb-3">
                    <label for="input-old-password" class="form-label">Password Lama</label>
                    <input name="old-password" type="password" class="form-control" id="input-old-password" required>
                </div>


                <div class="mb-3">
                    <label for="input-password" class="form-label">Password Baru</label>
                    <input name="password" type="password" class="form-control" id="input-password" required>
                </div>

                <div class="mb-3">
                    <label for="input-re-password" class="form-label">Ulangi Password Baru</label>
                    <input name="re-password" type="password" class="form-control" id="input-re-password" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
@endsection
