<?php
$servername = "localhost";
$username = "root";
$password = "2820";
$dbname = "ferreteriafer";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Actualizar la categoría si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_categoria = $_POST['id_categoria'];
    $nombre_categoria = $_POST['nombre_categoria'];

    $updateQuery = "UPDATE categoria SET nombre_categoria = ? WHERE id_categoria = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $nombre_categoria, $id_categoria);
    $updateStmt->execute();
}

// Consulta para obtener todas las categorías
$query = "SELECT * FROM categoria";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Categorías</title>
    <link rel="stylesheet" href="listar.css">
</head>
<body>
    <header>
        <h1>Listar Categorías</h1>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_categoria']; ?></td>
                        <td><?= $row['nombre_categoria']; ?></td>
                        <td>
                            <button class="button" onclick="document.getElementById('editForm<?= $row['id_categoria']; ?>').style.display='block'">Editar</button> |
                            <a href="eliminar_categoria.php?id=<?= $row['id_categoria']; ?>" onclick="return confirm('¿Está seguro de eliminar esta categoría?');">Eliminar</a>
                        </td>
                    </tr>
                    <tr id="editForm<?= $row['id_categoria']; ?>" style="display:none;">
                        <td colspan="3">
                            <form method="POST">
                                <input type="hidden" name="id_categoria" value="<?= $row['id_categoria']; ?>">
                                <label for="nombre_categoria">Nombre de la Categoría:</label>
                                <input type="text" id="nombre_categoria" name="nombre_categoria" value="<?= htmlspecialchars($row['nombre_categoria']); ?>" required>
                                <button type="submit" class="button">Actualizar</button>
                                <button type="button" class="button" onclick="document.getElementById('editForm<?= $row['id_categoria']; ?>').style.display='none'">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="panel.php" class="button">Regresar al Panel</a>
    </main>
    <footer>
        <p>&copy; 2024 Ferretería. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
