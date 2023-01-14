@extends('mobile.layouts.master')

@section('style')
    <!-- datepicker css -->
    {{-- <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css"> --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection

@section('script')
    <!-- datepicker js -->
    {{-- <script src="/assets/libs/flatpickr/flatpickr.min.js"></script> --}}

    <script>
        flatpickr("#input-date")
    </script>
@endsection

@section('content')
    @include('mobile.layouts.navbar')
    <div class="row px-3 py-3">
        <div class="col">
            <form action="{{ route('mobile.leave.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label for="input-date" class="form-label">Tanggal</label>
                    <input name="date" type="text" class="form-control" id="input-date" value="{{ old('date') }}"
                        placeholder="YYYY-MM-DD" required>
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
    </div>
@endsection
