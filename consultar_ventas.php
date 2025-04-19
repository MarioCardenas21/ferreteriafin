<?php
include 'conexion.php';

$query_ventas = "
    SELECT v.id_venta, v.fecha, v.total, c.nombre AS nombre_cliente,
           COALESCE(e.nombre, 'No asignado') AS nombre_empleado
    FROM venta v
    JOIN cliente c ON v.id_cliente = c.id_cliente
    LEFT JOIN empleado e ON v.id_empleado = e.id_empleado
    ORDER BY v.fecha DESC
";
$result_ventas = $conn->query($query_ventas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Ventas</title>
    <link rel="stylesheet" href="estilo_consultar_ventas.css">
</head>
<body>
    <header class="header">
        <h1>Consultar Ventas</h1>
        <h2>Ferretería</h2>
    </header>

    <main class="main-container">
        <table class="sales-table">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Total (Q)</th>
                    <th>Reimprimir</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_ventas->num_rows > 0): ?>
                    <?php while ($venta = $result_ventas->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id_venta']); ?></td>
                            <td><?= htmlspecialchars($venta['fecha']); ?></td>
                            <td><?= htmlspecialchars($venta['nombre_cliente']); ?></td>
                            <td><?= htmlspecialchars($venta['nombre_empleado']); ?></td>
                            <td><?= number_format($venta['total'], 2); ?></td>
                            <td>
                                <a href="factura.php?id_venta=<?= htmlspecialchars($venta['id_venta']); ?>" class="button" target="_blank">🧾 Reimprimir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay ventas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="panel.php" class="button">🔙 Regresar al Panel</a>
        </div>
    </main>

   
</body>
</html>

<?php $conn->close(); ?>
