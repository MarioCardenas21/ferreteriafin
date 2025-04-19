<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "UPDATE proveedor SET activo = 0 WHERE id_proveedor = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: proveedor.php?msg=eliminado");
        exit;
    } else {
        echo "Error al eliminar proveedor.";
    }

    $stmt->close();
}
$conn->close();
