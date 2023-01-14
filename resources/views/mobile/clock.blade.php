@extends('mobile.layouts.master')

@section('style')
    <!-- leaflet Css -->
    <link href="/assets/libs/leaflet/leaflet.css" rel="stylesheet" type="text/css" />
@endsection

@section('script')
    <!-- leaflet plugin -->
    <script src="/assets/libs/leaflet/leaflet.js"></script>

    <script>
        const inputLat = document.getElementById("input-lat")
        const inputLng = document.getElementById("input-lng")

        var map = L.map('leaflet-map-marker').setView([-7.82783, -249.62927], 10);
        var marker = null;

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

        L.circle([{{ auth()->user()->school->lat }}, {{ auth()->user()->school->lng }}], {
            color: '#28b765',
            fillColor: '#28b765',
            fillOpacity: 0.5,
            radius: {{ auth()->user()->school->distance }}
        }).addTo(map);

        const options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        function success(pos) {
            const crd = pos.coords;
            inputLat.value = crd.latitude
            inputLng.value = crd.longitude
            if (marker == null) {
                marker = L.marker([crd.latitude, crd.longitude]).addTo(map)
            } else {
                marker.setLatLng([crd.latitude, crd.longitude]);
            }
            map.setView([crd.latitude, crd.longitude], 20)

        }

        function error(err) {
            console.log(err)
            alert('Lokasi gagal ditemukan.')
        }

        function getLocation() {
            navigator.geolocation.getCurrentPosition(success, error, options);
        }
        getLocation()

        var date = new Date();
        var n = date.toISOString()
            .split("T")[0];
        var hour = date.getHours();
        if (hour < 10) hour = "0" + hour
        var time = hour + ":" + date.getMinutes();

        document.getElementById('date').innerHTML = n;
        document.getElementById('time').innerHTML = time;
    </script>
@endsection

@section('content')
    @include('mobile.layouts.navbar')
    <div class="mx-3">
        <div class="card mt-3" style="height: 500px">
            <div id="leaflet-map-marker" style="height: 500px"></div>
        </div>

        <div class="mt-3">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-border-left alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-block-helper me-3 align-middle"></i>
                        {{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif

        </div>

        <div class="row mt-5">
            <div class="col text-center">
                <h4 id="time" style="font-weight: 700;"></h4>
                <h6 id="date"></h6>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col">
                <button class="btn btn-outline-primary w-100" onclick="getLocation()">Perbaharui lokasi</button>
            </div>
        </div>

        <form class="row g-3 mt-1 mb-5" method="POST">
            <input type="hidden" id="input-lat" name="lat" value="" />
            <input type="hidden" id="input-lng" name="lng" value="" />

            @csrf

            <div class="col-6">
                <button class="btn btn-outline-primary w-100" formaction="{{ route('mobile.clock.in') }}">Presensi
                    Masuk</button>
            </div>

            <div class="col-6">
                <button class="btn btn-primary w-100" formaction="{{ route('mobile.clock.out') }}">Presensi
                    Pulang</button>
            </div>
        </form>

    </div>
@endsection
