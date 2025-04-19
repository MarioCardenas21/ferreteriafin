<?php
include 'conexion.php';

if (isset($_GET['restaurar'])) {
    $id = intval($_GET['restaurar']);
    $stmt = $conn->prepare("UPDATE cliente SET activo = 1 WHERE id_cliente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: clientes_inactivos.php");
    exit;
}

$query = "SELECT id_cliente, nombre, telefono FROM cliente WHERE activo = 0";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes Inactivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">🗃 Clientes Inactivos</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Restaurar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_cliente']; ?></td>
                    <td><?= htmlspecialchars($row['nombre']); ?></td>
                    <td><?= htmlspecialchars($row['telefono']); ?></td>
                    <td>
                        <a href="?restaurar=<?= $row['id_cliente']; ?>" class="btn btn-success btn-sm">✔ Restaurar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="gestor_clientes.php" class="btn btn-secondary mt-3">🔙 Volver al Gestor</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
