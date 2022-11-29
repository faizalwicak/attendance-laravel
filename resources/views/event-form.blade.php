@extends('layouts.master')

@section('style')

<!-- datepicker css -->
<link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">

@endsection

@section('script')

<!-- datepicker js -->
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

<script>
    flatpickr("#datepicker-date")
</script>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $event ? '/event/'.$event->id : '/event' }}" method="POST">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
            
            @csrf
            @if ($event) 
            @method('PUT')
            @endif

            <div class="mb-3">
                <label for="datepicker-date" class="form-label">Tanggal</label>
                <input name='date' type="text" class="form-control flatpickr-input" id="datepicker-date" value="{{ old('date', $event ? $event->date : date('Y-m-d')) }}" required>
            </div>


            <div class="mb-3">
                <label for="input-description" class="form-label">Deskripsi Kegiatan</label>
                <input name="description" type="text" class="form-control" id="input-description" value="{{ old('description', $event ? $event->description : '') }}" required>
            </div>
            
            <div class="mb-3">
                <label for="select-type" class="form-label">Tipe</label>
                <select name='type' class="form-select" id="select-type" aria-label="Kelas">
                    <option value="HOLIDAY" {{old('type', $event ? $event->type : '') == 'HOLIDAY' ? 'selected' : ''}}>Hari Libuar</option>
                    <option value="EVENT" {{old('type', $event ? $event->type : '') == 'EVENT' ? 'selected' : ''}}>Event</option>
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