@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="/student/importAction" method="POST" enctype="multipart/form-data">
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
                <label for="input-file" class="form-label">Template Import Data</label>
                <br>
                <a href="/assets/template/user-template.xlsx">user-template.xlsx</a>
            </div>

            <div class="mb-3">
                <label for="input-file" class="form-label">File (xls, xlsx) *</label>
                <input name="file" class="form-control" type="file" id="input-file" value="{{ old('file') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-md">Simpan</button>
            </div>
        </form>
    </div>
    <!-- end card body -->
</div>
@endsection