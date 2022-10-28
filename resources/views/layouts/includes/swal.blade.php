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
    @endif
</script>