<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reclutados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Reset y tipografía */
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: #0c0c0c; /* negro profundo */
            color: #ffffff;
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            margin-bottom: 12px;
            color: #00aaff; /* azul destacado como en el banner */
        }

        small {
            color: #bbbbbb;
            display: block;
            margin-bottom: 24px;
        }

        /* Tabla moderna */
        table {
            border-collapse: collapse;
            width: 100%;
            background: #1a1a1a; /* fondo ligeramente más claro para la tabla */
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 170, 255, 0.2);
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background: linear-gradient(90deg, #0d47a1, #1976d2); /* azul degradado */
            color: #fff;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #111111;
        }

        tr:hover {
            background: #222222;
        }

        td {
            border-bottom: 1px solid #333;
        }

        /* Icono de empresa al principio */
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .logo span {
            font-size: 1.5em;
            font-weight: bold;
            color: #00aaff;
        }

        .credits {
            text-align: right;
            margin: 20px;
            color: #bbbbbb;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">
            <img src="{{ asset('images/grupoprocontacto_logo.jpg') }}" alt="Logo ProContacto">
            <span>ProContacto</span>
        </div>
        <h1>Reclutados</h1>
        <small>Datos obtenidos desde la Base de Datos de Procontacto. 
            Los datos se encuentran normalizados y se eliminaron los registros duplicados para una mejor lectura.
        </small>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Edad</th>
                    <th>Tipo de Documento</th>
                    <th>Número de Documento</th>
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

    <p class="credits">API creada por Luis Demetrio Di Nicco</p>
</body>
</html>