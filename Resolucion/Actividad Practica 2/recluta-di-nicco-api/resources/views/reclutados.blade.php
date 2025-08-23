<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reclutados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 24px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f5f5f5; }
        tr:nth-child(even) { background: #fafafa; }
        .wrap { max-width: 1100px; margin: 0 auto; }
        h1 { margin-bottom: 12px; }
        small { color: #666; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Reclutados</h1>
    <small>Datos obtenidos desde Firebase y normalizados para lectura.</small>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido (suraname)</th>
                <th>Birthday</th>
                <th>Age</th>
                <th>Document Type</th>
                <th>Document Number</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($rows as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['suraname'] }}</td>
                <td>{{ $r['birthday'] }}</td>
                <td>{{ $r['age'] }}</td>
                <td>{{ $r['documentType'] }}</td>
                <td>{{ $r['documentNumber'] }}</td>
            </tr>
        @empty
            <tr><td colspan="6">Sin datos para mostrar.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
