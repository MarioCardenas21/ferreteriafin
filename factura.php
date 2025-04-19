<?php
include 'conexion.php';

$id_venta = $_GET['id_venta'] ?? null;
if (!$id_venta) {
    die("Error: ID de venta no recibido.");
}

$stmt = $conn->prepare("SELECT v.id_venta, v.fecha, v.total, c.nombre AS cliente, c.telefono FROM venta v JOIN cliente c ON v.id_cliente = c.id_cliente WHERE v.id_venta = ?");
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc() ?? [];

if (!$venta) {
    die("Error: Venta no encontrada.");
}

$stmt = $conn->prepare("SELECT p.nombre, dv.cantidad, dv.precio_unitario, (dv.cantidad * dv.precio_unitario) AS subtotal FROM detalle_venta dv JOIN producto p ON dv.id_producto = p.id_producto WHERE dv.id_venta = ?");
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$detalle = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?= htmlspecialchars($venta['id_venta']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
            padding: 3rem;
            font-family: 'Segoe UI', sans-serif;
        }
        .factura-box {
            max-width: 900px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px #ccc;
        }
        .title {
            text-align: center;
            font-size: 28px;
            margin-bottom: 1rem;
            letter-spacing: 1px;
        }
        .info-cliente, .info-factura {
            font-size: 14px;
        }
        .table thead th {
            background-color: #e9ecef;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .total-box {
            text-align: right;
            margin-top: 1rem;
            font-size: 18px;
        }
        .footer-msg {
            text-align: center;
            font-style: italic;
            margin-top: 40px;
            font-size: 14px;
            color: #555;
        }
        .btn-print {
            display: block;
            margin: 2rem auto 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            @page {
                margin: 0;
            }
            .no-pdf {
                display: none !important;
            }
            .factura-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
<div class="factura-box" id="factura">
    <div class="title">FACTURA</div>

    <div class="d-flex justify-content-between mb-3">
        <div class="info-factura">
            <strong>Ferretería Baruch</strong><br>
            123 Calle Principal<br>
            Ciudad, País<br>
            Tel: 1234-5678
        </div>
        <div class="info-cliente text-end">
            <strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente']) ?><br>
            <strong>Teléfono:</strong> <?= htmlspecialchars($venta['telefono']) ?><br>
            <strong>Fecha:</strong> <?= htmlspecialchars($venta['fecha']) ?><br>
            <strong>N° Factura:</strong> <?= htmlspecialchars($venta['id_venta']) ?>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 10%;">Cant.</th>
            <th>Descripción</th>
            <th style="width: 20%;">Precio Unitario</th>
            <th style="width: 20%;">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $detalle->fetch_assoc()): ?>
            <tr>
                <td><?= $row['cantidad'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td>Q<?= number_format($row['precio_unitario'], 2) ?></td>
                <td>Q<?= number_format($row['subtotal'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total-box">
        <strong>Total: Q<?= number_format($venta['total'], 2) ?></strong>
    </div>

    <div class="footer-msg">
        Gracias por su compra. ¡Vuelva pronto!
    </div>

    <button id="btnPdf" class="btn btn-primary btn-print no-pdf">⬇ Descargar PDF</button>
</div>

<!-- Librería html2pdf.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.getElementById("btnPdf").addEventListener("click", function () {
        const factura = document.getElementById("factura");

        const numFactura = "<?= $venta['id_venta']; ?>";
        const cliente = "<?= preg_replace('/\s+/', '_', $venta['cliente']); ?>";
        const fecha = "<?= date('Y-m-d', strtotime($venta['fecha'])); ?>";
        const nombreArchivo = `factura_${numFactura}_${cliente}_${fecha}.pdf`;

        // Ocultar botón de descarga antes de generar PDF
        const elementosOcultos = document.querySelectorAll(".no-pdf");
        elementosOcultos.forEach(el => el.style.display = 'none');

        const opt = {
            margin: 0.5,
            filename: nombreArchivo,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(factura).save().then(() => {
            // Mostrar botón de nuevo
            elementosOcultos.forEach(el => el.style.display = '');
        });
    });
</script>
</body>
</html>

<?php $conn->close(); ?>
