@php
    $headers = ['Başlangıç tarihi', 'Bitiş tarihi', 'Label', 'Gelir', 'Para birimi', 'Yüzde'];
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
                <td>{{ $earning->label_name }}</td>
                <td>{{ $earning->earning }}</td>
                <td>USD</td>
                <td>{{ $earning->percentage }}</td>
            </tr>
        @endforeach
    </tbody>
</table>