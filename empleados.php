<?php
// Incluir la conexión a la base de datos
include 'conexion.php';

// Inicializar las variables para los mensajes
$msg = "";
$error = "";

// Procesar formulario para agregar/editar/eliminar empleados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["accion"]) && $_POST["accion"] == "agregar") {
        // Obtener los datos del formulario
        $nombre = $_POST["nombre"];
     

        // Preparar la consulta SQL para agregar un empleado
        $stmt = $conn->prepare("INSERT INTO empleado (nombre_empleado, telefono_empleado, email_empleado, direccion_empleado, puesto) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre);
        if ($stmt->execute()) {
            $msg = "Empleado agregado exitosamente.";
        } else {
            $error = "Error al agregar el empleado: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST["accion"]) && $_POST["accion"] == "eliminar") {
        // Eliminar un empleado
        $id_empleado = $_POST["id_empleado"];
        $stmt = $conn->prepare("DELETE FROM empleado WHERE id_empleado = ?");
        $stmt->bind_param("i", $id_empleado);
        if ($stmt->execute()) {
            $msg = "Empleado eliminado exitosamente.";
        } else {
            $error = "Error al eliminar el empleado: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_consultar_ventass.css"> <!-- Vincula el archivo CSS -->

    <title>Gestión de Empleados</title>
   
<body>
<header>
    <h1>Gestión de Empleados</h1>
</header>
<main>
    <?php if ($msg): ?>
        <div class="message success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Formulario para agregar empleado -->
    <form method="POST" action="">
        <h2>Agregar Empleado</h2>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="hidden" name="accion" value="agregar">
        <button type="submit">Agregar</button>
    </form>

    <!-- Listado de empleados -->
    <h2>Listado de Empleados</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
          
        </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM empleado");
        while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row["id_empleado"]; ?></td>
                <td><?php echo $row["nombre_empleado"]; ?></td>
                <td>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="id_empleado" value="<?php echo $row["id_empleado"]; ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</main>
</body>
</html>
