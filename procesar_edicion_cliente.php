<?php
include 'conexion.php';

$id_cliente = intval($_POST['id_cliente']);
$nombre = trim($_POST['nombre']);
$telefono = trim($_POST['telefono']);

if (empty($nombre)) {
    die("❌ El nombre es obligatorio.");
}

$stmt = $conn->prepare("UPDATE cliente SET nombre = ?, telefono = ? WHERE id_cliente = ?");
$stmt->bind_param("ssi", $nombre, $telefono, $id_cliente);

if ($stmt->execute()) {
    header("Location: gestor_clientes.php?editado=1");
} else {
    echo "❌ Error al actualizar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
