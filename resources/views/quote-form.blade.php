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
        var latitude = e.latlng['lat'];
        var longitude = e.latlng['lng'];
        if (longitude < -180) {
            longitude += 360;
        }
        if (longitude > 180) {
            longitude -=360;
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

@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ $quote ? '/quote/'.$quote->id : '/quote/store' }}" method="POST">
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
                    <option value="0" {{ old('active', $quote ? $quote->active : '') == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="1" {{ old('active', $quote ? $quote->active : '') == 1 ? 'selected' : '' }}>Aktif</option>
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