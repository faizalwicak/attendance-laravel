@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $link ? '/important-link/' . $link->id : '/important-link' }}" method="POST">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf
                @if ($link)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="input-title" class="form-label">Judul</label>
                    <input name="title" type="text" class="form-control" id="input-title" required
                        value="{{ old('title', $link ? $link->title : '') }}">
                </div>

                <div class="mb-3">
                    <label for="input-link" class="form-label">Link</label>
                    <input name="link" type="text" class="form-control" id="input-link" required
                        value="{{ old('link', $link ? $link->link : '') }}">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-md">Simpan</button>
                </div>
            </form>
        </div>
        <!-- end card body -->
    </div>
@endsection
