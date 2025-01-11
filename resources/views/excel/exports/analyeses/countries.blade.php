<table>
    <thead>
    <tr>
        <th>Başlangıç tarihi</th>
        <th>Bitiş tarihi</th>
        <th>Ülke</th>
        <th>Gelir</th>
        <th>Para birimi</th>
        <th>Yüzde</th>
    </tr>
    </thead>
    <tbody>
    @foreach($eaarnings as $earning)
        <tr>
            <td>{{ $earning->start_date }}</td>
            <td>{{ $earning->end_date }}</td>
            <td>{{ $earning->country }}</td>
            <td>USD</td>
            <td>{{ $earning->end_date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>