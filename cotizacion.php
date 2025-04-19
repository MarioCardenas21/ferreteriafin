<?php
include 'conexion.php';

$productos = $_POST['productos'] ?? [];
$cliente = $_POST['id_cliente'];
$nuevo_nombre = $_POST['nuevo_nombre'] ?? '';
$nuevo_telefono = $_POST['nuevo_telefono'] ?? '';

if ($cliente === 'nuevo') {
    $cliente_nombre = $nuevo_nombre;
    $cliente_telefono = $nuevo_telefono;
} else {
    $stmt = $conn->prepare("SELECT nombre, telefono FROM cliente WHERE id_cliente = ?");
    $stmt->bind_param("i", $cliente);
    $stmt->execute();
    $stmt->bind_result($cliente_nombre, $cliente_telefono);
    $stmt->fetch();
    $stmt->close();
}

$detalles = [];
$total = 0;

foreach ($productos as $id => $cantidad) {
    $cantidad = intval($cantidad);
    if ($cantidad < 1) continue;

    $stmt = $conn->prepare("SELECT nombre, precio FROM producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $precio);
    if ($stmt->fetch()) {
        $subtotal = $precio * $cantidad;
        $detalles[] = [
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'precio' => $precio,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
    $stmt->close();
}

$fecha = date('Y-m-d');
$cliente_filename = preg_replace('/\s+/', '_', $cliente_nombre);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización - Ferretería Baruch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 3rem;
            background: #fff;
            font-family: 'Segoe UI', sans-serif;
        }
        .cotizacion-box {
            max-width: 900px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 30px;
        }
        .title {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .info {
            font-size: 14px;
        }
        .btn-print {
            display: block;
            margin: 2rem auto 0;
        }
        @media print {
            .no-pdf { display: none !important; }
            @page { margin: 0; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
<div class="cotizacion-box" id="cotizacion">
    <div class="title">COTIZACIÓN</div>

    <div class="d-flex justify-content-between mb-3">
        <div class="info">
            <strong>Ferretería Baruch</strong><br>
            123 Calle Principal<br>
            Ciudad, País<br>
            Tel: 1234-5678
        </div>
        <div class="info text-end">
            <strong>Cliente:</strong> <?= htmlspecialchars($cliente_nombre) ?><br>
            <strong>Teléfono:</strong> <?= htmlspecialchars($cliente_telefono) ?><br>
            <strong>Fecha:</strong> <?= $fecha ?>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
        <tr>
            <th style="width: 10%;">Cant.</th>
            <th>Producto</th>
            <th style="width: 20%;">Precio Unitario</th>
            <th style="width: 20%;">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($detalles as $d): ?>
            <tr>
                <td><?= $d['cantidad'] ?></td>
                <td><?= htmlspecialchars($d['nombre']) ?></td>
                <td>Q<?= number_format($d['precio'], 2) ?></td>
                <td>Q<?= number_format($d['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-end fs-5 mt-3"><strong>Total: Q<?= number_format($total, 2) ?></strong></div>

    <div class="text-center mt-4 fst-italic text-muted">
        Esta es una cotización informativa, no representa una venta real.
    </div>

    <button class="btn btn-outline-primary btn-print no-pdf" onclick="window.print()">🖨️ Imprimir</button>
</div>

</body>
</html>

<?php $conn->close(); ?>
