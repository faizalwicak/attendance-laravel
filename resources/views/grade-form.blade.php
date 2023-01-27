@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $grade ? '/grade/' . $grade->id : '/grade' }}" method="POST">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf
                @if ($grade)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="input-name" class="form-label">Nama Kelas</label>
                    <input name="name" type="text" class="form-control" id="input-name"
                        value="{{ old('name', $grade ? $grade->name : '') }}" placeholder="misal: Kelas 10-1" required>
                </div>

                <div class="mb-3">
                    <label for="input-grade" class="form-label">Tinkatan Kelas</label>
                    <select name="grade" id="input-grade" class="form-select" required>
                        <option value="">-- pilih kelas --</option>
                        <option value="10" {{ old('grade', $grade ? $grade->grade : '') == '10' ? 'selected' : '' }}>
                            Kelas 10</option>
                        <option value="11" {{ old('grade', $grade ? $grade->grade : '') == '11' ? 'selected' : '' }}>
                            Kelas 11</option>
                        <option value="12" {{ old('grade', $grade ? $grade->grade : '') == '12' ? 'selected' : '' }}>
                            Kelas 12</option>
                        <option value="0" {{ old('grade', $grade ? $grade->grade : '') == '0' ? 'selected' : '' }}>
                            GTK</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-md">Simpan</button>
                </div>
            </form>
        </div>
        <!-- end card body -->
    </div>
@endsection
