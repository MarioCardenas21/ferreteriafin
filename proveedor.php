<?php
include 'conexion.php';

// Obtener productos para sugerencia en Select2
$productos_existentes = [];
$result_productos = $conn->query("SELECT nombre FROM producto ORDER BY nombre ASC");
while ($row = $result_productos->fetch_assoc()) {
    $productos_existentes[] = $row['nombre'];
}

// Insertar proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_proveedor'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $productos = is_array($_POST['productos']) ? $_POST['productos'] : [];
    $marcas = is_array($_POST['marcas']) ? $_POST['marcas'] : [];

    if ($nombre && $telefono) {
        $detalles = '';
        if (!empty($productos)) {
            $detalles .= "Productos: " . implode(', ', $productos);
        }
        if (!empty($marcas)) {
            if ($detalles !== '') $detalles .= ", ";
            $detalles .= "Marcas: " . implode(', ', $marcas);
        }

        $stmt = $conn->prepare("INSERT INTO proveedor (nombre, telefono, direccion, Detalles, activo) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("ssss", $nombre, $telefono, $direccion, $detalles);
        $stmt->execute();
        $stmt->close();

        header("Location: proveedor.php?success=1");
        exit;
    }
}

// Obtener proveedores activos
$query = "SELECT * FROM proveedor WHERE activo = 1";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        body { background-color: #f8f9fa; padding: 2rem; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">📦 Lista de Proveedores</h2>

    <form method="POST" class="border p-3 mb-4 bg-white rounded">
        <h5 class="mb-3">➕ Agregar Nuevo Proveedor</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre_proveedor" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Dirección (opcional)</label>
                <input type="text" name="direccion" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Productos</label>
                <select name="productos[]" id="productos" class="form-select" multiple>
                    <?php foreach ($productos_existentes as $producto): ?>
                        <option value="<?= htmlspecialchars($producto) ?>"><?= htmlspecialchars($producto) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Marcas</label>
                <select name="marcas[]" id="marcas" class="form-select" multiple></select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-30">💾 Guardar Proveedor</button>
        <a href="panel.php" class="btn btn-secondary w-30">🔙 Regresar al Panel</a>

    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Detalles</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['direccion']) ?></td>
                <td><?= htmlspecialchars($row['Detalles']) ?></td>
                <td>
                    <a href="editar_proveedor.php?id=<?= $row['id_proveedor'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <a href="eliminar_proveedor.php?id=<?= $row['id_proveedor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Deseas ocultar este proveedor?');">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#productos, #marcas').select2({
            tags: true,
            tokenSeparators: [','],
            placeholder: "Escribe o selecciona...",
            width: '100%'
        });
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
