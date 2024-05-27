<style>
    td{
        border: 2px solid #000;
        padding: 10px 10px;
        text-align: center;
    }
</style>
<table style="margin: auto;">
    <tr>
        <th>id</th>
        <th>name</th>
        <th>EPS</th>
        <th>P/E</th>
        <th>R/EG</th>
        <th>P/S</th>
    </tr>
    @foreach ($datas as $data)
    <tr>
        <td>{{ $data->id }}</td>
        <td>{{ $data->name }}</td>
        <td>{{ $data->EPS }}</td>
        <td>{{ $data->PE }}</td>
        <td>{{ $data->PEG }}</td>
        <td>{{ $data->PS }}</td>
        <td>{{ $data->IR }}</td>
    </tr>
    @endforeach
</table>