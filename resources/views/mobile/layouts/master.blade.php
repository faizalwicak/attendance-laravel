<!doctype html>
<html lang="en" class="bg-light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Presensi Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    {{-- <style>
        html {
            min-height: 100vh;
            max-width: 600px;
            margin: 0 auto;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter',
                sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Inter',
                sans-serif;
        }

        p {
            font-family: 'Inter',
                sans-serif;
        }

        hr {
            padding: 0;
            margin: 0;
        }
    </style> --}}

    @yield('style')
</head>

<body class="bg-white">
    <div class="container-fluid p-0" style="min-height: 100vh">
        @yield('content')
    </div>
    @include('mobile.layouts.bottom-navbar')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>

    <script>
        @if (session('success'))
            alert('{{ session('success') }}')
        @elseif (session('error'))
            alert('{{ session('error') }}')
        @elseif (session('warning'))
            alert('{{ session('warning') }}')
        @endif
    </script>
    @yield('script')
</body>

</html>
