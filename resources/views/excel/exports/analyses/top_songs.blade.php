@php
    $headers = ['Başlangıç tarihi', 'Bitiş tarihi', 'Şarkı Adı', 'Sanatçı', 'ISRC', 'Streams', 'Gelir', 'Para birimi', 'Yüzde'];
@endphp

<table>
    <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($earnings as $earning)
            <tr>
                <td>{{ $earning->start_date }}</td>
                <td>{{ $earning->end_date }}</td>
                <td>{{ $earning->song_name }}</td>
                <td>{{ $earning->artist_name }}</td>
                <td>{{ $earning->isrc_code }}</td>
                <td>{{ $earning->streams }}</td>
                <td>{{ $earning->earning }}</td>
                <td>USD</td>
                <td>{{ $earning->percentage }}</td>
            </tr>
        @endforeach
    </tbody>
</table>