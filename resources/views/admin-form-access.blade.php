@extends('layouts.master')

@section('style')
<!-- choices css -->
<link href="/assets/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('script')
<!-- choices js -->
<script src="/assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    new Choices("#input-access",{removeItemButton:!0, choices: [
        @foreach ($grades as $grade)
        {
            value: '{{$grade->id}}',
            label: '{{$grade->name}}',
            selected: {{in_array($grade->id, $selectedGrades) ? 'true' : 'false'}},
        },
        @endforeach
    ]})
</script>
@endsection

@section('content')
{{-- // @foreach ($grades as $grade)
        //     <option value="{{$grade->id}}">{{$grade->name}}</option>
        // @endforeach --}}
<div class="card">
    <div class="card-body">
        <form action="{{'/admin/'.$user->id.'/access'}}" method="POST">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
            
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="input-access" class="form-label">Kelas</label>                
                <select class="form-control" name="access[]" id="input-access" placeholder="-- Pilih kelas --" multiple>
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