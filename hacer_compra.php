<?php
include 'conexion.php'; // Conexión a la base de datos

// Obtener proveedores
$query_proveedores = "SELECT id_proveedor, nombre FROM proveedor";
$result_proveedores = $conn->query($query_proveedores);

// Obtener productos con sus proveedores
$query_productos = "SELECT id_producto, nombre, precio FROM producto";
$result_productos = $conn->query($query_productos);

// Procesar la compra si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proveedor = $_POST['id_proveedor'];
    $productos = $_POST['productos'];

    $total_compra = 0;
    $detalle_productos = [];

    // Procesar los productos y calcular el total
    foreach ($productos as $producto) {
        $id_producto = intval($producto['id_producto']);
        $cantidad = intval($producto['cantidad']);
        $precio_unitario = floatval($producto['precio_unitario']);
        
        // Calcular subtotal de cada producto
        $subtotal = $precio_unitario * $cantidad;
        $total_compra += $subtotal;

        $detalle_productos[] = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario
        ];
    }

    // Registrar la compra en la base de datos
    $query_compra = "INSERT INTO compra (id_proveedor, total) VALUES (?, ?)";
    $stmt_compra = $conn->prepare($query_compra);
    $stmt_compra->bind_param("id", $id_proveedor, $total_compra);
    $stmt_compra->execute();
    $id_compra = $stmt_compra->insert_id;
    $stmt_compra->close();

    // Registrar detalles de la compra
    $query_detalle = "INSERT INTO detalle_compra (id_compra, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($query_detalle);
    foreach ($detalle_productos as $producto) {
        $stmt_detalle->bind_param("iiid", $id_compra, $producto['id_producto'], $producto['cantidad'], $producto['precio_unitario']);
        $stmt_detalle->execute();
    }
    $stmt_detalle->close();

    echo "<script>
            alert('Compra registrada con éxito. ID de la compra: $id_compra.');
            window.location.href = 'detalle_compra.php?id_compra=$id_compra';
        </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Compras</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Gestión de Compras</h1>
    </header>

    <main>
        <form method="POST" action="hacer_compras.php">
            <!-- Proveedor -->
            <label for="id_proveedor">Proveedor:</label>
            <select name="id_proveedor" id="id_proveedor" required>
                <?php while ($row = $result_proveedores->fetch_assoc()): ?>
                    <option value="<?= $row['id_proveedor']; ?>"><?= $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Productos -->
            <h3>Productos:</h3>
            <div id="producto-container">
                <div class="producto">
                    <select name="productos[0][id_producto]" required onchange="actualizarPrecio(this)">
                        <?php while ($row = $result_productos->fetch_assoc()): ?>
                            <option value="<?= $row['id_producto']; ?>" data-precio="<?= $row['precio']; ?>"><?= $row['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="number" name="productos[0][cantidad]" min="1" placeholder="Cantidad" required>
                    <input type="hidden" name="productos[0][precio_unitario]" value="0">
                </div>
            </div>
            <button type="button" onclick="agregarProducto()">Agregar otro producto</button>

            <button type="submit">Registrar Compra</button>
        </form>

        <a href="panel.php" class="button">Regresar al Panel</a>
    </main>

    <script>
        let contadorProductos = 1;

        function agregarProducto() {
            const container = document.getElementById('producto-container');
            const div = document.createElement('div');
            div.classList.add('producto');
            div.innerHTML = `
                <select name="productos[${contadorProductos}][id_producto]" required onchange="actualizarPrecio(this)">
                    <?php
                    $result_productos->data_seek(0);
                    while ($row = $result_productos->fetch_assoc()):
                    ?>
                        <option value="<?= $row['id_producto']; ?>" data-precio="<?= $row['precio']; ?>"><?= $row['nombre']; ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" name="productos[${contadorProductos}][cantidad]" min="1" placeholder="Cantidad" required>
                <input type="hidden" name="productos[${contadorProductos}][precio_unitario]" value="0">
            `;
            container.appendChild(div);
            contadorProductos++;
        }

        function actualizarPrecio(select) {
            const precio = select.options[select.selectedIndex].getAttribute('data-precio');
            const inputPrecio = select.parentNode.querySelector('input[type=hidden]');
            inputPrecio.value = precio;
        }
    </script>
</body>
</html>
