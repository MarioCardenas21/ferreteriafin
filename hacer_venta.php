<?php
include 'conexion.php';

$query_clientes = "SELECT id_cliente, nombre, telefono FROM cliente ORDER BY nombre ASC";
$result_clientes = $conn->query($query_clientes);

$query_productos = "SELECT id_producto, nombre, precio, stock FROM producto WHERE activo = 1 ORDER BY nombre ASC";
$productos = [];
$result_productos = $conn->query($query_productos);
while ($prod = $result_productos->fetch_assoc()) {
    $productos[] = $prod;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Venta o Cotización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            padding: 2rem;
            background-color: #f8f9fa;
        }
        .nuevo-cliente {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #e9ecef;
            border-radius: 0.5rem;
        }
        .producto-cantidad {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Registrar Venta o Cotización</h1>

    <form method="POST" action="" id="ventaCotForm">
        <div class="mb-3">
            <label for="id_cliente" class="form-label">Seleccionar Cliente</label>
            <select class="form-select" name="id_cliente" id="id_cliente" onchange="toggleNuevoCliente(this.value)" required>
                <option value="">Seleccione un cliente...</option>
                <?php while ($row = $result_clientes->fetch_assoc()): ?>
                    <option value="<?= $row['id_cliente']; ?>">
                        <?= htmlspecialchars($row['nombre']); ?> (<?= $row['telefono']; ?>)
                    </option>
                <?php endwhile; ?>
                <option value="nuevo">+ Agregar nuevo cliente</option>
            </select>
        </div>

        <div class="nuevo-cliente" id="nuevoCliente">
            <h5>Nuevo Cliente</h5>
            <div class="mb-2">
                <label>Nombre:</label>
                <input type="text" name="nuevo_nombre" class="form-control" placeholder="Nombre completo">
            </div>
            <div class="mb-2">
                <label>Teléfono:</label>
                <input type="text" name="nuevo_telefono" class="form-control" placeholder="Teléfono">
            </div>
        </div>

        <div class="mb-3">
            <label for="producto_selector" class="form-label">Buscar productos</label>
            <select class="form-select" id="producto_selector" multiple>
                <?php foreach ($productos as $prod): ?>
                    <option value="<?= $prod['id_producto']; ?>"
                            data-nombre="<?= htmlspecialchars($prod['nombre']); ?>"
                            data-precio="<?= $prod['precio']; ?>"
                            data-stock="<?= $prod['stock']; ?>">
                        <?= htmlspecialchars($prod['nombre']); ?> (Q<?= number_format($prod['precio'], 2); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="cantidades-container" class="producto-cantidad"></div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-success w-50 me-2" onclick="submitForm('ventas.php')">💰 Hacer Venta</button>
            <button type="button" class="btn btn-info w-50 ms-2" onclick="submitForm('cotizacion.php')">📄 Cotizar</button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="panel.php" class="btn btn-secondary w-50">🔙 Regresar al Panel</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const productos = <?= json_encode($productos); ?>;

    $(document).ready(function () {
        $('#producto_selector').select2({
            placeholder: 'Buscar productos...',
            allowClear: true
        });

        $('#producto_selector').on('change', function () {
            const seleccionados = $(this).val();
            const contenedor = $('#cantidades-container');
            contenedor.empty();

            if (seleccionados) {
                seleccionados.forEach(id => {
                    const producto = productos.find(p => p.id_producto === id);
                    if (!producto) return;

                    const stock = parseInt(producto.stock);
                    contenedor.append(`
                        <div class="mb-2">
                            <label>${producto.nombre} - Disponible: ${stock} (Q${parseFloat(producto.precio).toFixed(2)})</label>
                            <input type="number" class="form-control"
                                name="productos[${id}]"
                                min="1"
                                max="${stock}"
                                placeholder="Cantidad (máx ${stock})"
                                oninput="if(this.value > ${stock}) this.value = ${stock};"
                            >
                        </div>
                    `);
                });
            }
        });
    });

    function toggleNuevoCliente(valor) {
        document.getElementById("nuevoCliente").style.display = (valor === 'nuevo') ? 'block' : 'none';
    }

    function submitForm(action) {
        const form = document.getElementById('ventaCotForm');
        const cliente = document.getElementById('id_cliente').value;
        const productosSeleccionados = $('#producto_selector').val();
        let error = '';

        if (!cliente) {
            error = 'Por favor, seleccione un cliente.';
        } else if (!productosSeleccionados || productosSeleccionados.length === 0) {
            error = 'Debe seleccionar al menos un producto.';
        } else {
            let cantidadValida = false;
            productosSeleccionados.forEach(id => {
                const input = document.querySelector(`[name="productos[${id}]"]`);
                if (input && parseInt(input.value) > 0) {
                    cantidadValida = true;
                }
            });

            if (!cantidadValida) {
                error = 'Debe ingresar cantidades válidas (mayores a cero).';
            }
        }

        if (error) {
            alert(error);
        } else {
            form.action = action;
            form.submit();
        }
    }
</script>
</body>
</html>

<?php $conn->close(); ?>
