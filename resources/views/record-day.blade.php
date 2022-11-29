@extends('layouts.master')

@section('style')

<!-- gridjs css -->
<link rel="stylesheet" href="/assets/libs/gridjs/theme/mermaid.min.css">

<!-- datepicker css -->
<link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">

@endsection

@section('script')

<!-- gridjs js -->
<script src="/assets/libs/gridjs/gridjs.umd.js"></script>

<!-- datepicker js -->
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

<script>
// Basic Table
new gridjs.Grid({
  columns:
    [
        {
            name: 'Logo',
            sort: false,
            formatter: (function (cell) {
                return gridjs.html('<img src="/assets/images/logo-dark-sm.png" alt="" class="avatar-sm rounded-circle me-2" />');
            }),
            width: '50px',
        },
        {
            name: 'Username',
            width: '100px',
        },
        {
            name: 'Nama',
        },
        {
            name: 'Jenis Kelamin',
            width: '100px',
        },
        {
            name: 'Status',
            formatter: function (cell) {
              if (cell == '1') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-warning font-size-12">IZIN</span>
                `);
              } else if (cell == '0') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-success font-size-12">HADIR</span>
                `);
              } else {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-danger font-size-12">BELUM PRESENSI</span>
                `);
              }
            }
        },
        {
            name: 'Keterangan',
            formatter: function (cell) {
              if (cell == 'ON_TIME') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-success font-size-12">TEPAT WAKTU</span>
                `);
              } else if (cell == 'LATE') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-danger font-size-12">TELAT</span>
                `);
              } else if (cell == 'SICK') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-warning font-size-12">SAKIT</span>
                `);
              } else if (cell == 'LEAVE') {
                return gridjs.html(`
                  <span class="badge badge-pill badge-soft-warning font-size-12">IZIN</span>
                `);
              } else {
                return '-';
              }
            }
        },
        {
            name: 'Jam Masuk',
        },
        {
            name: 'Jam Pulang',
        },
        {
          name: ". . .",
          width: '100px',
          formatter: (function (cell) {
              if (cell != "") 
              return gridjs.html(`
                  <div class="d-flex gap-3">
                    <a href="/record/${cell}" type="button" class="btn btn-primary btn-sm btn-rounded">Detail</a>
                  </div>`);
              else return "";
          })
        },
    ],
  sort: true,
  search: true,
  data: [
    @foreach($users as $user)
    ["", "{{ $user->username }}", "{{ $user->name }}", "{{ $user->gender == 'MALE' ? 'L' : 'P' }}", "{{ count($user->records) > 0 ? $user->records[0]->is_leave : '' }}", "{{ count($user->records) > 0 ? $user->records[0]->is_leave ? $user->records[0]->leave->type : $user->records[0]->attend->clock_in_status : '' }}", "{{count($user->records) > 0 && $user->records[0]->attend ? $user->records[0]->attend->clock_in_time : "-"}}", "{{ count($user->records) > 0 && $user->records[0]->attend ? $user->records[0]->attend->clock_out_time : "-"}}", "{{ count($user->records) > 0 ? $user->records[0]->id : '' }}"],
    @endforeach
  ],
  pagination: {
    enabled: false,
    limit: 50,
    summary: false
  }
}).render(document.getElementById("table-data"));

document.getElementById("select-grade").addEventListener('change', function () {
    location.href = "/record/day?grade=" + this.value + "&day={{$selectedDay}}"
})

flatpickr("#datepicker-grade", {
  onChange: function(selectedDates, dateStr, instance) {
    location.href = "/record/day?grade={{$selectedGrade->id}}&day=" + dateStr
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
                      <input type="text" class="form-control flatpickr-input" id="datepicker-grade" readonly="readonly" value="{{$selectedDay}}">
                      <label for="select-grade">Periode</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="select-grade" aria-label="Kelas">
                            @foreach ($grades as $grade)
                            <option value="{{$grade->id}}" {{$selectedGrade->id == $grade->id ? 'selected' : ''}}>{{$grade['name']}}</option>
                            @endforeach
                        </select>
                        <label for="select-grade">Kelas</label>
                    </div>
                </div>
              </div>
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