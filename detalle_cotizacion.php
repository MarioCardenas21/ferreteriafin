<?php
// Incluir el archivo de conexión (aunque no se usa para la cotización, puede ser útil si se desea extender)
include 'conexion.php';

// Obtener los productos y detalles de la cotización desde la URL (se pasa un JSON con los productos)
$productos = isset($_GET['productos']) ? json_decode($_GET['productos'], true) : []; // Se pasa un JSON con los productos
$total_cotizacion = 0; // Total de la cotización

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización - Detalles</title>
    <!-- Incluir el archivo de estilo -->
    <link rel="stylesheet" type="text/css" href="estilo_detalle_venta.css">
</head>
<body>

    <h2>Detalles de la Cotización</h2>

    <table>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>

        <?php
        // Mostrar los productos de la cotización
        foreach ($productos as $producto) {
            $subtotal = $producto['cantidad'] * $producto['precio_unitario'];
            $total_cotizacion += $subtotal;
        ?>
            <tr>
                <td><strong><?php echo $producto['nombre_producto']; ?></strong></td> <!-- Nombre del producto -->
                <td><?php echo $producto['cantidad']; ?></td> <!-- Cantidad del producto -->
                <td><?php echo number_format($producto['precio_unitario'], 2); ?> Q</td> <!-- Precio unitario con símbolo de moneda -->
                <td><?php echo number_format($subtotal, 2); ?> Q</td> <!-- Subtotal con símbolo de moneda -->
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong><?php echo number_format($total_cotizacion, 2); ?> Q</strong></td> <!-- Total de la cotización -->
        </tr>
    </table>

    <p><strong>Total de la Cotización:</strong> Q<?php echo number_format($total_cotizacion, 2); ?> </p>

    <!-- Botón de imprimir -->
    <button onclick="imprimirCotizacion()">Imprimir Cotización</button>

    <!-- Botón de regresar -->
    <button onclick="window.history.back()">Regresar</button>

    <!-- Script JavaScript para imprimir -->
    <script>
        function imprimirCotizacion() {
            window.print();
        }
    </script>

</body>
</html>

<?php
// Cerrar la conexión (aunque no se usó en este caso, es bueno tenerlo)
$conn->close();
?>
