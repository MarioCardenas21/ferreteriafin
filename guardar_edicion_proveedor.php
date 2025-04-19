<?php
include 'conexion.php';

$id = intval($_POST['id_proveedor']);
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$detalles = trim($_POST['detalles'] ?? '');

if (!$id || empty($nombre) || empty($telefono)) {
    die("Campos obligatorios faltantes.");
}

$query = "UPDATE proveedor SET nombre = ?, telefono = ?, direccion = ?, Detalles = ? WHERE id_proveedor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $nombre, $telefono, $direccion, $detalles, $id);

if ($stmt->execute()) {
    header("Location: proveedor.php?msg=editado");
    exit;
} else {
    echo "Error al actualizar: " . $stmt->error;
}

$stmt->close();
$conn->close();
