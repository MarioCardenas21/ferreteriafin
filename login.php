<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="estilologin.css">
</head>
<body>
    <header>
        <h1>Iniciar Sesión</h1>
    </header>
    <form action="panel.php" method="POST">
        <input type="text" name="nombre_usuario" placeholder="Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>
    <footer class="footer">
        <p>&copy; 2024 Ferretería. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
