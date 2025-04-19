<?php
require 'conexion.php';

// Función para obtener el precio actual del producto
function obtenerPrecioProducto($id_producto) {
    global $conn;
    $stmt = $conn->prepare("SELECT precio FROM producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($precio);
    return $stmt->fetch() ? $precio : null;
}

// Obtener acción desde el formulario
$accion = $_POST['accion'] ?? '';
$id_empleado = 1; // Se puede usar el ID del empleado logueado si manejas sesiones

// Paso 1: determinar el cliente (nuevo o existente)
if ($_POST['id_cliente'] === 'nuevo') {
    $nuevo_nombre = trim($_POST['nuevo_nombre'] ?? '');
    $nuevo_telefono = trim($_POST['nuevo_telefono'] ?? '');

    if (empty($nuevo_nombre) || empty($nuevo_telefono)) {
        die("⚠️ Debes ingresar nombre y teléfono para el nuevo cliente.");
    }

    $stmt = $conn->prepare("INSERT INTO cliente (nombre, telefono) VALUES (?, ?)");
    $stmt->bind_param("ss", $nuevo_nombre, $nuevo_telefono);
    $stmt->execute();
    $id_cliente = $stmt->insert_id;
    $stmt->close();
} else {
    $id_cliente = intval($_POST['id_cliente']);
}

// Paso 2: preparar productos
$productos = [];
foreach ($_POST['productos'] as $id_producto => $cantidad) {
    $id_producto = intval($id_producto);
    $cantidad = intval($cantidad);
    if ($cantidad > 0) {
        $productos[] = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad
        ];
    }
}

if (empty($productos)) {
    die("⚠️ Debes seleccionar al menos un producto con cantidad válida.");
}

// Si es cotización, NO registrar venta, solo mostrar mensaje
if ($accion === 'cotizar') {
    echo "<h2 class='text-center mt-4'>📄 Cotización simulada</h2>";
    echo "<p class='text-center'><strong>Cliente:</strong> ID $id_cliente</p>";
    echo "<ul>";
    foreach ($productos as $p) {
        $precio = obtenerPrecioProducto($p['id_producto']) ?? 0;
        $subtotal = $precio * $p['cantidad'];
        echo "<li>Producto ID {$p['id_producto']} × {$p['cantidad']} → Q" . number_format($subtotal, 2) . "</li>";
    }
    echo "</ul>";
    echo "<div class='text-center mt-3'><a href='panel.php' class='btn btn-secondary'>← Volver al Panel</a></div>";
    exit;
}

// Paso 3: registrar venta real
$resultado = registrarVenta($id_cliente, $id_empleado, $productos);

if ($resultado['success']) {
    header("Location: factura.php?id_venta=" . $resultado['id_venta']);
    exit;
} else {
    echo "❌ Error: " . $resultado['error'];
}


// --- Lógica de la función principal ---
function registrarVenta($id_cliente, $id_empleado, $productos) {
    global $conn;
    $conn->begin_transaction();
    try {
        // Insertar la venta
        $stmt = $conn->prepare("INSERT INTO venta (id_cliente, id_empleado, total) VALUES (?, ?, 0)");
        $stmt->bind_param("ii", $id_cliente, $id_empleado);
        $stmt->execute();
        $id_venta = $stmt->insert_id;

        $total = 0;

        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $cantidad = $producto['cantidad'];

            $precio_unitario = obtenerPrecioProducto($id_producto);
            if ($precio_unitario === null) {
                throw new Exception("Producto no encontrado (ID: $id_producto)");
            }

            $subtotal = $cantidad * $precio_unitario;
            $total += $subtotal;

            // Insertar detalle de venta
            $stmt = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $id_venta, $id_producto, $cantidad, $precio_unitario);
            $stmt->execute();

            // Descontar stock
            $stmt = $conn->prepare("UPDATE producto SET stock = stock - ? WHERE id_producto = ?");
            $stmt->bind_param("ii", $cantidad, $id_producto);
            $stmt->execute();
        }

        // Actualizar total
        $stmt = $conn->prepare("UPDATE venta SET total = ? WHERE id_venta = ?");
        $stmt->bind_param("di", $total, $id_venta);
        $stmt->execute();

        $conn->commit();
        return ["success" => true, "id_venta" => $id_venta];
    } catch (Exception $e) {
        $conn->rollback();
        return ["success" => false, "error" => $e->getMessage()];
    }
}
?>
