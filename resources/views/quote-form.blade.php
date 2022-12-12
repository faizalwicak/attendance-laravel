@extends('layouts.master')

@section('style')
@endsection

@section('script')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $quote ? '/quote/' . $quote->id : '/quote' }}" method="POST">
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                @csrf
                @if ($quote)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="input-message" class="form-label">Pesan</label>
                    <textarea name="message" type="text" class="form-control" id="input-message" required>{{ old('message', $quote ? $quote->message : '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="input-active" class="form-label">Status</label>
                    <select id="input-active" name="active" class="form-select" required>
                        <option value="0" {{ old('active', $quote ? $quote->active : '') == 0 ? 'selected' : '' }}>
                            Tidak Aktif</option>
                        <option value="1" {{ old('active', $quote ? $quote->active : '') == 1 ? 'selected' : '' }}>
                            Aktif</option>
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
