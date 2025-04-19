<?php
include 'conexion.php';

$nombre = trim($_POST['nombre_proveedor'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

$productos = is_array($_POST['productos']) ? $_POST['productos'] : [];
$marcas = is_array($_POST['marcas']) ? $_POST['marcas'] : [];

// Validación básica
if (empty($nombre) || empty($telefono)) {
    die("⚠️ Nombre y teléfono son obligatorios.");
}

// Generar campo detalles
$detalles = '';
if (!empty($productos)) {
    $detalles .= "Productos: " . implode(', ', $productos);
}
if (!empty($marcas)) {
    if ($detalles !== '') $detalles .= ", ";
    $detalles .= "Marcas: " . implode(', ', $marcas);
}

// Insertar proveedor
$stmt = $conn->prepare("INSERT INTO proveedor (nombre, direccion, telefono, Detalles) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $direccion, $telefono, $detalles);

if ($stmt->execute()) {
    header("Location: proveedor.php?success=1");
    exit;
} else {
    echo "Error al guardar proveedor: " . $stmt->error;
}

$stmt->close();
$conn->close();
