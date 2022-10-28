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

<script>
    flatpickr("#input-clockin",{enableTime:!0,noCalendar:!0,dateFormat:"H:i",time_24hr:!0})
    flatpickr("#input-clockout",{enableTime:!0,noCalendar:!0,dateFormat:"H:i",time_24hr:!0})

    var initLat = parseFloat(document.getElementById('input-lat').value)
    var initLng = parseFloat(document.getElementById('input-lng').value)
    
    var map = null
    var circle = null

    if (!isNaN(initLat) && !isNaN(initLng)) {
        var map = L.map('leaflet-map-marker').setView([initLat, initLng], 20);
    } else {
        var map = L.map('leaflet-map-marker').setView([-7.82783, -249.62927], 10);
    }

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
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
        document.getElementById('input-lat').value = e.latlng['lat'].toFixed(5);
        document.getElementById('input-lng').value = e.latlng['lng'].toFixed(5);

        if (circle == null) {
            circle = L.circle([51.508, -0.11], {
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

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $school ? route('school.update', $school->id) : route('school.store') }}" method="POST">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>{{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
            
            @csrf
            @if ($school) 
            @method('PUT')
            @endif

            <div class="mb-3">
                <label for="input-name" class="form-label">Nama Sekolah</label>
                <input name="name" type="text" class="form-control" id="input-name" value="{{ old('name', $school ? $school->name : '') }}" required>
            </div>

            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="input-clockin" class="form-label">Jam Masuk</label>
                        <input name="clock_in" value="{{ old('clock_in', $school ? $school->clock_in : '') }}" type="text" class="form-control flatpickr-input active" id="input-clockin" readonly="readonly">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="input-clockout" class="form-label">Jam Pulang</label>
                        <input name="clock_out" value="{{ old('clock_out', $school ? $school->clock_out : '') }}" type="text" class="form-control flatpickr-input active" id="input-clockout" readonly="readonly">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="input-name" class="form-label">Lokasi</label>
                <div id="leaflet-map-marker" class="leaflet-map"></div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="input-lat" class="form-label">Latitude</label>
                        <input name="lat" value="{{ old('lat', $school ? $school->lat : '') }}" type="number" step="0.001" class="form-control" id="input-lat" readonly>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="input-lng" class="form-label">Longitude</label>
                        <input name="lng" value="{{ old('lng', $school ? $school->lng : '') }}" type="number" step="0.001" class="form-control" id="input-lng" readonly>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="input-distance" class="form-label">Jarak Jangkauan (meter)</label>
                        <input name="distance" value="{{ old('distance', $school ? $school->distance : '50') }}" type="number" class="form-control" id="input-distance" value="10" required>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-md">Simpan</button>
            </div>
        </form>
    </div>
    <!-- end card body -->
</div>
@endsection