@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="/me/profile" method="POST" enctype="multipart/form-data">
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
                <label for="input-username" class="form-label">Username</label>
                <input name="username" type="text" class="form-control" id="input-username" value="{{ old('username', $user ? $user->username : '') }}" required>
            </div>

            <div class="mb-3">
                <label for="input-name" class="form-label">Nama</label>
                <input name="name" type="text" class="form-control" id="input-name" value="{{ old('name', $user ? $user->name : '') }}" required>
            </div>

            <div class="mb-3">
                <label for="input-email" class="form-label">Email</label>
                <input name="email" type="email" class="form-control" id="input-email" value="{{ old('email', $user ? $user->email : '') }}" required>
            </div>

            <div class="mb-3">
                <label for="input-name" class="form-label">Foto</label>
                @if ($user->image != null && $user->image != "")
                <br><br>
                <img src="/images/{{$user->image}}" alt="" class="avatar-lg rounded-circle me-2"/>
                <br><br>
                @endif
                <input name="image" class="form-control" type="file" id="input-image" value="{{ old('image', $user ? $user->image : '') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-md">Simpan</button>
            </div>
        </form>
    </div>
    <!-- end card body -->
</div>
@endsection