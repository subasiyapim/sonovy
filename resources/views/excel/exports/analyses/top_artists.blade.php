<table>
    <thead>
    <tr>
        <th>Başlangıç tarihi</th>
        <th>Bitiş tarihi</th>
        <th>Sanatçı</th>
        <th>Gelir</th>
        <th>Para birimi</th>
        <th>Yüzde</th>
        <th>Streams</th>
    </tr>
    </thead>
    <tbody>
    @foreach($earnings as $earning)
        <tr>
            <td>{{ $earning->start_date }}</td>
            <td>{{ $earning->start_end_date }}</td>
            <td>{{ $earning->artist }}</td>
            <td>{{ $earning->earning }}</td>
            <td>USD</td>
            <td>{{ $earning->percentage }}</td>
            <td>{{ $earning->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>