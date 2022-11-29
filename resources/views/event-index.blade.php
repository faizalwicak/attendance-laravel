@extends('layouts.master')

@section('style')

<!-- gridjs css -->
<link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

<!-- datepicker css -->
<link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">
<link rel="stylesheet" href="/assets/libs/flatpickr/plugins/monthSelect/style.css">

@endsection

@section('script')

<!-- gridjs js -->
<script src="/assets/libs/gridjs/gridjs.umd.js"></script>

<!-- datepicker js -->
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="/assets/libs/flatpickr/plugins/monthSelect/index.js"></script>

<script>
// Basic Table
new gridjs.Grid({
  columns:
    [
        {
          name: 'Tanggal',
          width: '100px'
        },
        {
            name: 'Deskripsi',
        },
        {
          name: 'Tipe',
          width: '100px'
        },
        {
            name: "...",
            sort: false,
            width: '100px',
            formatter: (function (cell) {
                return gridjs.html(`
                    <div class="d-flex gap-3">
                        <a href="/event/${cell}/edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" class="text-success">
                            <i class="mdi mdi-pencil font-size-18"></i>
                        </a>
                        <form action="/event/${cell}" method="POST" onsubmit="return confirm('Apakah anda yakin? Data yang sudah dihapus tidak dapat dikembalikan!')">
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
  pagination: {
    enabled: false,
    limit: 50,
    summary: false
  },
  data: [
      @foreach ($events as $e)
      ["{{$e->date}}", "{{$e->description}}", "{{$e->type == 'HOLIDAY' ? 'Hari Libur' : 'Event'}}", "{{$e->id}}"],
      @endforeach
  ]
}).render(document.getElementById("table-data"));

flatpickr("#datepicker-periode", {
  plugins: [
      new monthSelectPlugin({
        shorthand: true,
        dateFormat: "Y-m",
        altFormat: "Y F",
      })
  ],
  onChange: function(selectedDates, dateStr, instance) {
    location.href = "/event?month=" + dateStr
  },
})

</script>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row"> 
                    <div class="col-md-6">
                      <div class="form-floating mb-3">
                        <input type="text" class="form-control flatpickr-input" id="datepicker-periode" readonly="readonly" value="{{$selectedMonth}}">
                        <label for="datepicker-periode">Periode</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                  </div>
                </div>

                <div class="position-relative">
                    <div class="modal-button mt-2">
                        <div class="row align-items-start">
                            <div class="col-sm-auto">
                                
                            </div>
                            <div class="col-sm">
                                <div class="mt-3 mt-md-0 mb-3">
                                    <a href="/event/create" class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2"><i class="mdi mdi-plus me-1"></i> Tambah Event</a>
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