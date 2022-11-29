@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $user ? '/student/'.$user->id : '/student' }}" method="POST">
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
                <label for="input-email" class="form-label">Jenis Kelamin</label>
                <select name="gender" class="form-select" required>
                    <option value="">-- pilih --</option>
                    <option value="MALE" {{ old('gender', $user ? $user->gender : '') == 'MALE' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="FEMALE" {{ old('gender', $user ? $user->gender : '') == 'FEMALE' ? 'selected' : '' }}>Perempuan</option>
                </select>    
            </div>

            <div class="mb-3">
                <label for="input-email" class="form-label">Kelas</label>
                <select name="grade_id" class="form-select" required>
                    <option value="">-- pilih kelas --</option>
                    @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ old('grade_id', $user ? $user->grade_id : '') == ''.$grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                    @endforeach
                </select>    
            </div>

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

            <div>
                <button type="submit" class="btn btn-primary w-md">Simpan</button>
            </div>
        </form>
    </div>
    <!-- end card body -->
</div>
@endsection