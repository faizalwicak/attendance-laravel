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
            name: 'Kelas',
        },
        {
            name: 'Total',
            width: '50px',
        },
        {
            name: 'Hadir',
            width: '50px',
        },
        {
            name: 'Belum Hadir',
            width: '50px',
        },
        {
            name: 'Izin',
            width: '50px',
        },
        {
          name: ". . .",
          width: '50px',
          formatter: (function (cell) {
              return gridjs.html(`
                  <div class="d-flex gap-3">
                    <a href="/record/day?grade=${cell}&day={{date('Y-m-d')}}" type="button" class="btn btn-primary btn-sm btn-rounded">Detail</a>
                  </div>`);
          })
        },
    ],
  sort: true,
  search: true,
  data: [
    @foreach ($grade_array as $g)
      ["{{$g['name']}}", "{{$g['count']}}", "{{$g['attend']}}", "{{$g['count'] - $g['attend']}}", "{{$g['leave']}}", "{{$g['id']}}"],
    @endforeach
  ],
  pagination: {
    enabled: false,
    limit: 50,
    summary: false
  }
}).render(document.getElementById("table-grade"));


</script>
@endsection

@section('content')

<div class="row">
  <div class="col">
      <div class="card">
          <div class="card-body">
              <div class="row align-items-center">
                  <div class="col">
                      <h4 class="m-0">{{date('d-m-Y')}}</h4>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-3 col-md-6">
      <div class="card">
          <div class="card-body">
              <div class="row align-items-center">
                  <div class="col">
                      <p class="text-muted text-truncate mb-0 pb-1">Total Siswa</p>
                      <h4 class="mb-0 mt-2">{{$aggregate['count']}}</h4>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <p class="text-muted text-truncate mb-0 pb-1">Siswa Hadir</p>
                    <h4 class="mb-0 mt-2">{{$aggregate['attend']}}</h4>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <p class="text-muted text-truncate mb-0 pb-1">Siswa Belum Hadir</p>
                    <h4 class="mb-0 mt-2">{{$aggregate['count'] - $aggregate['attend']}}</h4>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <p class="text-muted text-truncate mb-0 pb-1">Siswa Izin</p>
                    <h4 class="mb-0 mt-2">{{$aggregate['leave']}}</h4>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

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
                              
                          </div>
                      </div>
                      <!-- end row -->
                  </div>
              </div>

              <div id="table-grade"></div>

          </div>
          <!-- end card body -->
      </div>
      <!-- end card -->
  </div>
  <!-- end col -->
</div>

@endsection