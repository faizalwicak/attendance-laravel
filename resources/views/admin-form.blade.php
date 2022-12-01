@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $user ? '/admin/'.$user->id : '/admin' }}" method="POST" enctype="multipart/form-data">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
            
            @csrf
            @if ($user) 
            @method('PUT')
            @endif

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

            @if (auth()->user()->role == 'SUPERADMIN')
            <div class="mb-3">
                <label for="input-school" class="form-label">Sekolah</label>
                <select id="input-school" name="school_id" class="form-select" required>
                    <option value="">-- pilih sekolah --</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ old('school_id', $user ? $user->school_id : '') == ''.$school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>    
            </div>                
            @else
            <div class="mb-3">
                <label for="input-role" class="form-label">Role</label>
                <select id="input-role" name="role" class="form-select" required>
                    <option value="">-- pilih role --</option>
                    <option value="ADMIN" {{ old('role', $user ? $user->role : '' ) == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="OPERATOR" {{ old('role', $user ? $user->role : '' ) == 'OPERATOR' ? 'selected' : '' }}>OPERATOR</option>
                </select>    
            </div>
            @endif

            <div class="mb-3">
                <label for="input-password" class="form-label">Password</label>
                <input name="password" type="password" class="form-control" id="input-password" {{ !$user ? 'required' : ''}}>
                @if ($user)
                <div id="help-password" class="form-text">Kosongkan password apabila tidak ingin mengubah.</div> 
                @endif
            </div>

            <div class="mb-3">
                <label for="input-re-password" class="form-label">Ulangi Password</label>
                <input name="re-password" type="password" class="form-control" id="input-re-password" {{ !$user ? 'required' : ''}}>
            </div>
            
            <div class="mb-3">
                <label for="input-name" class="form-label">Foto</label>
                @if ($user && $user->image != null && $user->image != "")
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