<table>
    <thead>
    <tr>
        <th>Başlangıç tarihi</th>
        <th>Bitiş tarihi</th>
        <th>Ülke</th>
        <th>Streams</th>
        <th>Gelir</th>
        <th>Para birimi</th>
        <th>Yüzde</th>
    </tr>
    </thead>
    <tbody>
    @foreach($earnings as $earning)
        <tr>
            <td>{{ $earning['start_date'] }}</td>
            <td>{{ $earning['end_date'] }}</td>
            <td>{{ $earning['country'] }}</td>
            <td>{{ $earning['quantity'] }}</td>
            <td>{{ $earning['earning'] }}</td>
            <td>USD</td>
            <td>{{ $earning['percentage'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>