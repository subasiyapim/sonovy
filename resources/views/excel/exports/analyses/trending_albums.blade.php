<table>
    <thead>
    <tr>
        <th>Albüm Adı</th>
        <th>Gelir</th>
        <th>Para birimi</th>
    </tr>
    </thead>
    <tbody>
    @foreach($earnings as $earning)
        <tr>
            <td>{{ $earning->release_name }}</td>
            <td>{{ $earning->earning }}</td>
            <td>USD</td>
        </tr>
    @endforeach
    </tbody>
</table>