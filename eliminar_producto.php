<?php
// Incluir la conexión una sola vez (no redefinir $conn)
include 'conexion.php';

// Validar que venga el ID
if (!isset($_GET['id_producto'])) {
    die("❌ No se proporcionó un ID de producto.");
}

$id_producto = intval($_GET['id_producto']);

// Opción: Validar si el producto tiene relación con ventas (opcional y recomendado)
/*
$query_check = "SELECT COUNT(*) FROM detalle_venta WHERE id_producto = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("i", $id_producto);
$stmt_check->execute();
$stmt_check->bind_result($conteo);
$stmt_check->fetch();
$stmt_check->close();

if ($conteo > 0) {
    die("❌ No puedes eliminar este producto porque ya fue vendido.");
}
*/

// Eliminar el producto
$query = "DELETE FROM producto WHERE id_producto = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_producto);

if ($stmt->execute()) {
    header("Location: inventarios.php");
    exit;
} else {
    echo "❌ Error al eliminar el producto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
