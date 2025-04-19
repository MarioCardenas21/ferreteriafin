<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);
    $nuevo_precio_compra = isset($_POST['nuevo_precio_compra']) ? floatval($_POST['nuevo_precio_compra']) : null;
    $nuevo_precio_venta  = isset($_POST['nuevo_precio_venta']) ? floatval($_POST['nuevo_precio_venta']) : null;

    if ($id_producto && $cantidad > 0) {
        // 1. Agregar stock
        $stmt = $conn->prepare("UPDATE producto SET stock = stock + ? WHERE id_producto = ?");
        $stmt->bind_param("ii", $cantidad, $id_producto);
        $stmt->execute();
        $stmt->close();

        // 2. Actualizar precios si vienen definidos
        if ($nuevo_precio_compra !== null || $nuevo_precio_venta !== null) {
            $campos = [];
            $params = [];
            $types = "";

            if ($nuevo_precio_compra !== null) {
                $campos[] = "Precio_compra = ?";
                $params[] = $nuevo_precio_compra;
                $types .= "d";
            }

            if ($nuevo_precio_venta !== null) {
                $campos[] = "precio = ?";
                $params[] = $nuevo_precio_venta;
                $types .= "d";
            }

            $params[] = $id_producto;
            $types .= "i";

            $sql = "UPDATE producto SET " . implode(", ", $campos) . " WHERE id_producto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
        }

        echo "<script>alert('✅ Stock actualizado correctamente.'); window.location.href='inventarios.php';</script>";
    } else {
        echo "<script>alert('❌ Datos inválidos.'); window.history.back();</script>";
    }
}

$conn->close();
?>
