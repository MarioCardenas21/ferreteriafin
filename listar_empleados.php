<?php
include 'conexion.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_empleado = $_POST['nombre_empleado'];
    $telefono_empleado = $_POST['telefono_empleado'];
    $email_empleado = $_POST['email_empleado'];
    $direccion_empleado = $_POST['direccion_empleado'];
    $puesto = $_POST['puesto'];

    $query = "INSERT INTO empleado (nombre_empleado, telefono_empleado, email_empleado, direccion_empleado, puesto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $nombre_empleado, $telefono_empleado, $email_empleado, $direccion_empleado, $puesto);
    $stmt->execute();

    echo "<script>alert('Empleado registrado con exito.'); window.location.href='panel.php';</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado</title>
    <link rel="stylesheet" href="ventaaas.css">
</head>
<body>
    <header>
        <h1>Agregar Empleado</h1>
    </header>
    <main>
        <form method="POST" action="">
            <label for="nombre_empleado">Nombre:</label>
            <input type="text" name="nombre_empleado" id="nombre_empleado" required>

            <label for="telefono_empleado">Telefono:</label>
            <input type="text" name="telefono_empleado" id="telefono_empleado" required>

            <label for="email_empleado">Email:</label>
            <input type="email" name="email_empleado" id="email_empleado" required>

            <label for="direccion_empleado">Direccion:</label>
            <input type="text" name="direccion_empleado" id="direccion_empleado" required>

            <label for="puesto">Puesto:</label>
            <input type="text" name="puesto" id="puesto" required>

            <button type="submit">Registrar Empleado</button>
        </form>
        <a href="panel.php" class="button">Regresar al Panel</a>
    </main>
    <footer>
        <p>&copy; 2024 Ferretería. Todos los derechos reservados.</p>
    </footer>
</body>
</html>


