@extends('layouts.master')

@section('style')

<!-- gridjs css -->
<link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

@endsection

@section('script')

<!-- gridjs js -->
<script src="/assets/libs/gridjs/gridjs.umd.js"></script>

<script>
// Basic Table
new gridjs.Grid({
  columns:
    [
        {
            name: 'Pesan',
        },
        {
            name: 'Status',
            formatter: (function (cell) {
                if (cell == '0') {
                    return gridjs.html(`<span class="badge badge-pill badge-soft-danger font-size-12">Tidak Aktif</span>`);
                }
                return gridjs.html(`<span class="badge badge-pill badge-soft-success font-size-12">Aktif</span>`)
            })
        },
        {
            name: "...",
            sort: false,
            width: '100px',
            formatter: (function (cell) {
                return gridjs.html(`
                    <div class="d-flex gap-3">
                        <a href="/quote/${cell}/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" class="text-success">
                            <i class="mdi mdi-pencil font-size-18"></i>
                        </a>
                        <form action="/quote/${cell}" method="POST" onsubmit="return confirm('Apakah anda yakin? Data yang sudah dihapus tidak dapat dikembalikan!')">
                            @csrf
                            @method('DELETE')
                            <button  data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus" class="text-danger" type="submit" style="background-color: transparent; background-repeat: no-repeat; border: none; cursor: pointer; overflow: hidden; outline: none;"><i class="mdi mdi-delete font-size-18""></i></button>
                        </form>
                    </div>`);
            })
      }
    ],
    sort: true,
    search: true,
    data: [
        @foreach($quotes as $quote)
        ["{{ $quote->message }}", "{{ $quote->active }}", "{{ $quote->id }}"],
        @endforeach
    ]
}).render(document.getElementById("table-data"));


</script>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm-auto">
                                
                            </div>
                            <div class="col-sm">
                                <div class="mt-3 mt-md-0 mb-3">
                                    <a href="/quote/create" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i class="mdi mdi-plus me-1"></i> Tambah Quote</a>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>

                <div id="table-data"></div>

            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end row -->
@endsection