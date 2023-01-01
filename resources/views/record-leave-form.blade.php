@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="/record/leave" method="POST" enctype="multipart/form-data">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf
                {{-- @if ($user)
                    @method('PUT')
                @endif --}}

                <div class="mb-3">
                    <label for="input-name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="input-name" readonly="readonly"
                        value="{{ $user->name }}">
                </div>

                <div class="mb-3">
                    <label for="input-grade" class="form-label">Kelas</label>
                    <input type="text" class="form-control" id="input-grade" readonly="readonly"
                        value="{{ $user->grade->name }}">
                </div>

                <input name="user_id" type="hidden" value="{{ $user->id }}" />

                <div class="mb-3">
                    <label for="input-date" class="form-label">Tanggal</label>
                    <input name="date" type="text" class="form-control" id="input-date" readonly="readonly"
                        value="{{ $selectedDay }}">
                </div>

                <div class="mb-3">
                    <label for="input-type" class="form-label">Tipe</label>
                    <select name="type" id="input-type" class="form-select" required>
                        <option value="SICK" {{ old('type') == 'SICK' ? 'selected' : '' }}>
                            Sakit
                        </option>
                        <option value="LEAVE" {{ old('type') == 'LEAVE' ? 'selected' : '' }}>
                            Izin
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="input-description" class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" id="input-description">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="input-file" class="form-label">Bukti Foto</label>
                    <input name="file" type="file" class="form-control" id="input-file">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-md">Simpan</button>
                </div>
            </form>
        </div>
        <!-- end card body -->
    </div>
@endsection
