<?php
include 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ ID de cliente no proporcionado.");
}

// Obtener datos del cliente
$stmt = $conn->prepare("SELECT nombre, telefono FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $telefono);
if (!$stmt->fetch()) {
    die("❌ Cliente no encontrado.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">✏️ Editar Cliente</h2>

    <form method="POST" action="procesar_edicion_cliente.php">
        <input type="hidden" name="id_cliente" value="<?= $id ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" class="form-control" name="telefono" id="telefono" value="<?= htmlspecialchars($telefono) ?>">
        </div>

        <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
        <a href="gestor_clientes.php" class="btn btn-secondary">← Cancelar</a>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
