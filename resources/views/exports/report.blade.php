<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            @foreach ($days as $d)
                <th>{{ explode('-', $d)[2] }}</th>
            @endforeach
            <th>Total</th>
            <th>Masuk</th>
            <th>Tepat Waktu (TW)</th>
            <th>Terlambat (TL)</th>
            <th>Sakit</th>
            <th>Izin</th>
            <th>Alpha</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                @foreach ($record as $r)
                    @if ($loop->index > 0)
                        <td>{{ $r }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
