<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $stock = intval($_POST['stock']);
    $precio = floatval($_POST['precio']);
    $precio_compra = floatval($_POST['Precio_compra'] ?? 0);
    $descripcion = trim($_POST['descripcion']);

    // Opcionales
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $id_proveedor = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;

    // Extras en descripción
    $marca = trim($_POST['marca'] ?? '');
    $material = trim($_POST['material'] ?? '');
    $medida = trim($_POST['medida'] ?? '');

    // Validación de existencia por nombre + marca + medida (case insensitive)
    $check_sql = "
        SELECT COUNT(*) FROM producto 
        WHERE LOWER(nombre) = LOWER(?) 
          AND LOWER(descripcion) LIKE CONCAT('%Marca: ', LOWER(?), '%') 
          AND LOWER(descripcion) LIKE CONCAT('%Medida: ', LOWER(?), '%')
    ";

    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $nombre, $marca, $medida);
    $check_stmt->execute();
    $check_stmt->bind_result($existe);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($existe > 0) {
        echo "<script>alert('⚠️ Ya existe un producto con ese nombre, marca y medida.'); window.history.back();</script>";
        exit;
    }

    // Insertar nuevo producto
    $stmt = $conn->prepare("
        INSERT INTO producto 
            (nombre, descripcion, id_categoria, id_proveedor, precio, stock, Precio_compra, activo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");

    if (!$stmt) {
        echo "<script>alert('❌ Error de preparación: " . $conn->error . "'); window.history.back();</script>";
        exit;
    }

    // Autocompletar descripción desde campos marca, material, medida
    $desc_final = '';
    if ($marca)    $desc_final .= "Marca: $marca, ";
    if ($material) $desc_final .= "Material: $material, ";
    if ($medida)   $desc_final .= "Medida: $medida";
    $desc_final = rtrim($desc_final, ', '); // quitar última coma si aplica

    $stmt->bind_param(
        "ssiidid",
        $nombre,
        $desc_final,
        $id_categoria,
        $id_proveedor,
        $precio,
        $stock,
        $precio_compra
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Producto agregado exitosamente.'); window.location.href='panel.php';</script>";
    } else {
        echo "<script>alert('❌ Error al agregar producto: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
