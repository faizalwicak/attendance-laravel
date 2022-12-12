@extends('layouts.master')

@section('style')
    <!-- datepicker css -->
    <link rel="stylesheet" href="/assets/libs/flatpickr/flatpickr.min.css">

    <!-- leaflet Css -->
    <link href="/assets/libs/leaflet/leaflet.css" rel="stylesheet" type="text/css" />
@endsection

@section('script')
    <!-- datepicker js -->
    <script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

    <!-- leaflet plugin -->
    <script src="/assets/libs/leaflet/leaflet.js"></script>

    @if (!$record->is_leave)
        <script>
            var initLat = parseFloat(document.getElementById('input-lat').value)
            var initLng = parseFloat(document.getElementById('input-lng').value)

            var map = null
            var circle = null

            if (!isNaN(initLat) && !isNaN(initLng)) {
                var map = L.map('leaflet-map-marker').setView([initLat, initLng], 20);
            } else {
                var map = L.map('leaflet-map-marker').setView([-7.82783, -249.62927], 10);
            }

            L.tileLayer(
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                    maxZoom: 18,
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1
                }).addTo(map);

            if (!isNaN(initLat) && !isNaN(initLng)) {
                circle = L.circle([initLat, initLng], {
                    color: '#28b765',
                    fillColor: '#28b765',
                    fillOpacity: 0.5,
                    radius: document.getElementById('input-distance').value
                }).addTo(map);
            }

            map.on('click', function(e) {
                var latitude = e.latlng['lat'];
                var longitude = e.latlng['lng'];
                if (longitude < -180) {
                    longitude += 360;
                }
                if (longitude > 180) {
                    longitude -= 360;
                }


                document.getElementById('input-lat').value = latitude.toFixed(6);
                document.getElementById('input-lng').value = longitude.toFixed(6);

                if (circle == null) {
                    circle = L.circle(e.latlng, {
                        color: '#28b765',
                        fillColor: '#28b765',
                        fillOpacity: 0.5,
                        radius: document.getElementById('input-distance').value
                    }).addTo(map);
                } else {
                    circle.setLatLng(e.latlng);
                }
            });

            document.getElementById('input-distance').addEventListener("change", function(e) {
                if (circle != null) {
                    circle.setRadius(parseInt(document.getElementById('input-distance').value));
                }
            });
        </script>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            @if ($record->is_leave)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-name" class="form-label">Status</label>
                            <input name="name" type="text" class="form-control" id="input-name" value="IZIN"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Keterangan</label>
                            <input name="name" type="text" class="form-control" id="input-name"
                                value="{{ $record->leave->type == 'LEAVE' ? 'IZIN' : 'SAKIT' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="input-name" class="form-label">Status Izin</label>
                    <input name="name" type="text" class="form-control" id="input-name"
                        value="{{ $record->leave->leave_status == 'WAITING' ? 'MENUNGGU' : ($record->leave->leave_status == 'ACCEPT' ? 'DIRETIMA' : 'DITOLAK') }}"
                        readonly>
                </div>

                @if ($record->leave->file != null && $record->leave->file != '')
                    <div class="mb-3">
                        <img src="/images/leave/{{ $record->leave->file }}" alt="" class="img-fluid" />
                    </div>
                @endif

                <div class="d-flex">
                    <form method="POST" action="/record/{{ $record->id }}">
                        @csrf
                        @method('put')
                        <input name="accept" value="1" type="hidden" />
                        <button type="submit" class="btn btn-success w-md"
                            @if ($record->leave->leave_status == 'ACCEPT') disabled @endif>Terima</button>
                    </form>
                    <form method="POST" action="/record/{{ $record->id }}">
                        @csrf
                        @method('put')
                        <input name="accept" value="0" type="hidden" />
                        <button type="submit" class="btn btn-danger w-md ms-2"
                            @if ($record->leave->leave_status == 'REJECT') disabled @endif>Tolak</button>
                    </form>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-name" class="form-label">Status</label>
                            <input name="name" type="text" class="form-control" id="input-name" value="HADIR"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Keterangan</label>
                            <input name="name" type="text" class="form-control" id="input-name"
                                value="{{ $record->attend->clock_in_status == 'ON_TIME' ? 'ON TIME' : 'TERLAMBAT' }}"
                                readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockin" class="form-label">Jam Masuk</label>
                            <input name="clock_in" value="{{ $record->attend->clock_in_time }}" type="text"
                                class="form-control flatpickr-input active" id="input-clockin" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input-clockout" class="form-label">Jam Pulang</label>
                            <input name="clock_out" value="{{ $record->attend->clock_out_time }}" type="text"
                                class="form-control flatpickr-input active" id="input-clockout" readonly="readonly">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="input-name" class="form-label">Lokasi</label>
                    <div id="leaflet-map-marker" class="leaflet-map"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-6">
                            <label for="input-lat" class="form-label">Latitude</label>
                            <input name="lat" value="{{ $record->attend->clock_in_lat }}" type="number"
                                class="form-control" id="input-lat" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="input-lng" class="form-label">Longitude</label>
                            <input name="lng" value="{{ $record->attend->clock_in_lng }}" type="number"
                                class="form-control" id="input-lng" readonly>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <!-- end card body -->
    </div>
@endsection
