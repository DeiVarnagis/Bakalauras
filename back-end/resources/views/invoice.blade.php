<?php

    $now = date('Y-m-d');

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table {
            font-family: sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        h2 {
            text-align: center
        }

    </style>
</head>

<body>

    @if ($type == 1)
    <h2>Ilgalaikis Turtas</h2>
    @else
    <h2>Trumpalaikis Turtas</h2>
    @endif

    <p>Turto savininkas: {{ $user->name }} {{ $user->surname }}</p>
    <p>Sukūrimo data: {{$now}} </p>
    <table>
        <tr>
            <th>Kodas</th>
            <th>Pavadinimas</th>
            <th>Serijos numeris</th>
            <th>Kiekis</th>
        </tr>
        @foreach ($data as $device)
            <tr>
                <td>{{ $device->code }}</td>
                <td>{{ $device->name }} </td>
                <td>{{ $device->serialNumber }}</td>
                <td>{{ $device->amount }}</td>
            </tr>
        @endforeach

    </table>

</body>

</html>
