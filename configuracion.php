<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Panel</title>
    <link rel="stylesheet" href="estilos.css"> <!-- Agrega aquí tu CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }
        .button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 15px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Configuración del Panel</h1>
    </header>
    <div class="container">
        <h2>Gestión</h2>
        <a href="listar_categorias.php" class="button">Gestión de Categorías</a>
        <a href="panel.php" class="button">Regresar al Panel Principal</a>
    </div>

</body>
</html>
