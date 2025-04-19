<?php
include 'conexion.php';

$query = "SELECT id_producto, nombre, stock FROM producto WHERE activo = 1 ORDER BY nombre ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto Existente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .container {
            max-width: 700px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">📦 Agregar Producto Existente</h2>

    <form method="POST" action="procesar_existente.php">
        <div class="mb-3">
            <label for="id_producto" class="form-label">Buscar Producto</label>
            <select class="form-select" name="id_producto" id="id_producto" required>
                <option value="">Selecciona un producto...</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['id_producto']; ?>">
                        <?= htmlspecialchars($row['nombre']); ?> (Stock actual: <?= $row['stock']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="cantidad" class="form-label">Cantidad a Agregar</label>
                <input type="number" class="form-control" name="cantidad" min="1" required>
            </div>
            <div class="col-md-4">
                <label for="nuevo_precio_compra" class="form-label">Nuevo Precio Compra (opcional)</label>
                <input type="number" class="form-control" name="nuevo_precio_compra" step="0.01">
            </div>
            <div class="col-md-4">
                <label for="nuevo_precio_venta" class="form-label">Nuevo Precio Venta (opcional)</label>
                <input type="number" class="form-control" name="nuevo_precio_venta" step="0.01">
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">✔ Agregar Stock</button>
            <a href="agregar_producto.php" class="btn btn-secondary">← Regresar</a>
        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#id_producto').select2({
            placeholder: "Escribe para buscar producto...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
