<?php
include 'conexion.php';

$nombre = trim($_POST['nombre']);
$telefono = trim($_POST['telefono']);

// Validación mínima
if (empty($nombre)) {
    die("❌ El nombre es obligatorio.");
}

if (!empty($_POST['id_cliente'])) {
    // Modo editar
    $id_cliente = intval($_POST['id_cliente']);
    $stmt = $conn->prepare("UPDATE cliente SET nombre = ?, telefono = ? WHERE id_cliente = ?");
    $stmt->bind_param("ssi", $nombre, $telefono, $id_cliente);
} else {
    // Modo agregar
    $stmt = $conn->prepare("INSERT INTO cliente (nombre, telefono) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $telefono);
}

if ($stmt->execute()) {
    header("Location: inventarios.php?cliente_ok=1");
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
