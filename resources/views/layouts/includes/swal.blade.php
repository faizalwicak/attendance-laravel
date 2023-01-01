<script>
    @if (session('success'))
        Swal.fire({
            title: 'Pesan',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#038edc',
        })
    @elseif (session('error'))
        Swal.fire({
            title: 'Pesan',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#038edc',
        })
    @elseif (session('warning'))
        Swal.fire({
            title: 'Pesan',
            text: '{{ session('warning') }}',
            icon: 'warning',
            confirmButtonColor: '#038edc',
        })
    @endif
</script>
