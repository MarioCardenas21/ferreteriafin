<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Obtener el ID de la venta de la URL
$id_venta = isset($_GET['id_venta']) ? $_GET['id_venta'] : 0;

// Consulta para obtener los detalles de la venta
$query_detalle = "
    SELECT v.id_venta, v.fecha_venta, v.total_venta, c.nombre_cliente, e.nombre_empleado, 
           d.id_producto, d.cantidad, p.nombre_producto, p.precio_unitario
    FROM venta v
    JOIN cliente c ON v.id_cliente = c.id_cliente
    JOIN empleado e ON v.id_empleado = e.id_empleado
    JOIN detalle_venta d ON v.id_venta = d.id_venta
    JOIN producto p ON d.id_producto = p.id_producto
    WHERE v.id_venta = ?
";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query_detalle);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result_detalle = $stmt->get_result();

// Verificar si hay resultados
if ($result_detalle->num_rows > 0) {
    $venta = $result_detalle->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Detalles de la Venta</title>
    <!-- Incluir el archivo de estilo -->
    <link rel="stylesheet" type="text/css" href="estilo_detalle_venta.css">
</head>
<body>

    <h2>Detalles de la Venta</h2>
    <h3>Factura ID: <?php echo $venta['id_venta']; ?></h3>
    <p><strong>Cliente:</strong> <?php echo $venta['nombre_cliente']; ?></p>
    <p><strong>Empleado:</strong> <?php echo $venta['nombre_empleado']; ?></p>
    <p><strong>Fecha:</strong> <?php echo $venta['fecha_venta']; ?></p>

    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>

        <?php
        $total_venta = 0;
        // Mostrar los detalles de la venta
        while ($row = $result_detalle->fetch_assoc()) {
            $subtotal = $row['cantidad'] * $row['precio_unitario'];
            $total_venta += $subtotal;
        ?>
            <tr>
                <td><strong><?php echo $row['nombre_producto']; ?></strong></td> <!-- Nombre del producto en negrita -->
                <td><?php echo $row['cantidad']; ?></td>
                <td><?php echo number_format($row['precio_unitario'], 2); ?> Q</td> <!-- Precio con símbolo de moneda -->
                <td><?php echo number_format($subtotal, 2); ?> Q</td> <!-- Subtotal con símbolo de moneda -->
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong><?php echo number_format($total_venta, 2); ?> </strong></td>
        </tr>
    </table>

    <p><strong>Total de la Venta:</strong> Q<?php echo number_format($venta['total_venta'], 2); ?> </p>

    <!-- Botón de imprimir -->
    <button onclick="imprimirFactura()">Imprimir Factura</button>

    <!-- Script JavaScript para imprimir -->
    <script>
        function imprimirFactura() {
            window.print();
        }
    </script>

</body>
</html>

<?php
} else {
    echo "No se encontró la venta.";  
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
