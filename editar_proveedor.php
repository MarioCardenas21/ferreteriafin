<?php
include 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID inválido.");
}

$query = "SELECT * FROM proveedor WHERE id_proveedor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$proveedor = $result->fetch_assoc();

if (!$proveedor) {
    die("Proveedor no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4 text-center">✏️ Editar Proveedor</h2>

    <form method="POST" action="guardar_edicion_proveedor.php">
        <input type="hidden" name="id_proveedor" value="<?= $proveedor['id_proveedor'] ?>">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($proveedor['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($proveedor['telefono']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($proveedor['direccion']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Detalles</label>
            <input type="text" name="detalles" class="form-control" value="<?= htmlspecialchars($proveedor['Detalles']) ?>">
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary w-50 me-2">💾 Guardar Cambios</button>
            <a href="proveedor.php" class="btn btn-secondary w-50 ms-2">← Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
