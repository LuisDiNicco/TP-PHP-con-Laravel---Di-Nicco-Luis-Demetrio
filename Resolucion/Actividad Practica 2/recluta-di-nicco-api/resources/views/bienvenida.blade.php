<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenida a la API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Reset y tipografía */
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: #0c0c0c;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            max-width: 600px;
            padding: 40px 20px;
        }

        h1 {
            color: #00aaff;
            margin-bottom: 12px;
        }

        p {
            color: #bbbbbb;
            margin-bottom: 30px;
        }

        /* Botón con estilo similar al banner */
        .btn {
            background: linear-gradient(90deg, #0d47a1, #1976d2);
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn:hover {
            background: linear-gradient(90deg, #1976d2, #0d47a1);
            box-shadow: 0 4px 12px rgba(0, 170, 255, 0.3);
        }

        /* Logo */
        .logo {
            display: flex;
            justify-content: center;
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
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 0.85em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/grupoprocontacto_logo.jpg') }}" alt="Logo ProContacto">
            <span>ProContacto</span>
        </div>

        <h1>Bienvenido/a a la API</h1>
        <p>Explora los reclutados registrados en Firebase desde una vista moderna y legible.</p>

        <a href="{{ url('/reclutados') }}" class="btn">Ver Reclutados</a>
    </div>

    <p class="credits">API creada por Luis Demetrio Di Nicco</p>
</body>
</html>
