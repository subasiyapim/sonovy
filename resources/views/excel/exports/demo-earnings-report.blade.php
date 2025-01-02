<table>
    <thead>
    <tr>
        <th>Rapor Ayı</th>
        <th>Satış Ayı</th>
        <th>Platform</th>
        <th>Ülke</th>
        <th>Label Adı</th>
        <th>Sanatçı Adı</th>
        <th>Albüm adı</th>
        <th>Parça adı</th>
        <th>UPC</th>
        <th>ISRC</th>
        <th>Albüm Katalog numarası</th>
        <th>Albüm tipi</th>
        <th>Satış Tipi</th>
        <th>Miktar</th>
        <th>Müşteri Ödeme Para Birimi</th>
        <th>Net Gelir</th>
        <th>Boş Sütun</th>
        <th>Plak şirketi oranı</th>
    </tr>
    </thead>
    <tbody>
    @foreach($earnings as $row)
        <tr>
            <td>{{$row['report_date']}}</td>
            <td>{{$row['sales_date']}}</td>
            <td>{{$row['platform']}}</td>
            <td>{{$row['country']}}</td>
            <td>{{$row['label_name']}}</td>
            <td>{{$row['artist_name']}}</td>
            <td>{{$row['release_name']}}</td>
            <td>{{$row['song_name']}}</td>
            <td>{{$row['upc_code']}}</td>
            <td>{{$row['isrc_code']}}</td>
            <td>{{$row['catalog_number']}}</td>
            <td>{{$row['release_type']}}</td>
            <td>{{$row['sales_type']}}</td>
            <td>{{$row['quantity']}}</td>
            <td>{{$row['currency']}}</td>
            <td>{{$row['earning']}}</td>
            <td>&nbsp;</td>
            <td>90</td>
        </tr>
    @endforeach
    </tbody>
</table>
