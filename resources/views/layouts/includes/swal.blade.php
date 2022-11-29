<script>
    @if (session('success'))
        Swal.fire(
            {
                title: 'Pesan',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#038edc',
            }
        )
    @elseif (session('danger'))
        Swal.fire(
            {
                title: 'Pesan',
                text: '{{ session('danger') }}',
                icon: 'danger',
                confirmButtonColor: '#038edc',
            }
        )
    @elseif (session('warning'))
        Swal.fire(
            {
                title: 'Pesan',
                text: '{{ session('warning') }}',
                icon: 'warning',
                confirmButtonColor: '#038edc',
            }
        )
    @endif
</script>