<table>
    <thead>
    <tr>
        <th>Başlangıç tarihi</th>
        <th>Bitiş tarihi</th>
        <th>Mağaza</th>
        <th>Streams</th>
        <th>Gelir</th>
        <th>Para birimi</th>
        <th>Yüzde</th>
    </tr>
    </thead>
    <tbody>
    @foreach($eaarnings as $earning)
        <tr>
            <td>{{ $earning->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>