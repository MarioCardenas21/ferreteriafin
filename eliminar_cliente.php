<?php
include 'conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ ID de cliente no proporcionado.");
}

$stmt = $conn->prepare("UPDATE cliente SET activo = 0 WHERE id_cliente = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: gestor_clientes.php?eliminado=1");
} else {
    echo "❌ Error al desactivar cliente: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
