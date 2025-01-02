<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>STATUS</th>
    </tr>
    </thead>
    <tbody>
    @foreach($broadcasts as $broadcast)
        <tr>
            <td>{{ $broadcast->id }}</td>
            <td>{{ $broadcast->status_text }}</td>
        </tr>
    @endforeach

    </tbody>
</table>
