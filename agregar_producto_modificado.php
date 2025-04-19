<?php include("conexion.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">➕ Agregar Producto Nuevo</h2>

    <form action="guardar_producto.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="categoria" class="form-label">Categoría (opcional)</label>
                <select id="categoria" name="id_categoria" class="form-select">
                    <option value="" selected>Sin Categoría</option>
                    <?php
                    $query = "SELECT id_categoria, nombre FROM categoria";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_categoria']}'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="proveedor" class="form-label">Proveedor (opcional)</label>
                <select id="proveedor" name="id_proveedor" class="form-select">
                    <option value="" selected>Sin Proveedor</option>
                    <?php
                    $query = "SELECT id_proveedor, nombre FROM proveedor";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_proveedor']}'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Cantidad</label>
                <input type="number" id="stock" name="stock" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="precio" class="form-label">Precio Venta</label>
                <input type="number" step="0.01" id="precio" name="precio" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="Precio_compra" class="form-label">Precio Compra</label>
                <input type="number" step="0.01" id="Precio_compra" name="Precio_compra" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="material" class="form-label">Material</label>
                <input type="text" id="material" name="material" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="medida" class="form-label">Medida</label>
                <input type="text" id="medida" name="medida" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control"
                      placeholder="Marca, material y medida se agregarán automáticamente..." rows="2" required></textarea>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">💾 Agregar Producto</button>
            <a href="panel.php" class="btn btn-secondary">🔙 Regresar al Panel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const marcaInput = document.getElementById('marca');
        const materialInput = document.getElementById('material');
        const medidaInput = document.getElementById('medida');
        const descripcionTextarea = document.getElementById('descripcion');

        function actualizarDescripcion() {
            let descripcion = '';

            if (marcaInput.value.trim()) {
                descripcion += 'Marca: ' + marcaInput.value.trim() + ', ';
            }
            if (materialInput.value.trim()) {
                descripcion += 'Material: ' + materialInput.value.trim() + ', ';
            }
            if (medidaInput.value.trim()) {
                descripcion += 'Medida: ' + medidaInput.value.trim();
            }

            descripcion = descripcion.replace(/,\s*$/, "");
            descripcionTextarea.value = descripcion;
        }

        marcaInput.addEventListener('input', actualizarDescripcion);
        materialInput.addEventListener('input', actualizarDescripcion);
        medidaInput.addEventListener('input', actualizarDescripcion);

        actualizarDescripcion();
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
