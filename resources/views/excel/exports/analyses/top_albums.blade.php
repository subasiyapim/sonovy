<table>
    <thead>
    <tr>
        <th>Başlangıç tarihi</th>
        <th>Bitiş tarihi</th>
        <th>Albüm</th>
        <th>Sanatçı</th>
        <th>UPC</th>
        <th>Streams</th>
        <th>Gelir</th>
        <th>Para birimi</th>
        <th>Yüzde</th>
    </tr>
    </thead>
    <tbody>
    @foreach($earnings as $earning)
        <tr>
            <td>{{ $earning->start_date }}</td>
            <td>{{ $earning->end_date }}</td>
            <td>{{ $earning->album_name }}</td>
            <td>{{ $earning->artist_name }}</td>
            <td>{{ $earning->upc_code }}</td>
            <td>{{ $earning->streams }}</td>
            <td>{{ $earning->earning }}</td>
            <td>USD</td>
            <td>{{ $earning->percentage }}</td>
        </tr>
    @endforeach
    </tbody>
</table>