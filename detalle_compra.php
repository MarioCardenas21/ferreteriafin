<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';  // Asegúrate de que la ruta a 'conexion.php' sea correcta

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha recibido la información del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    // Comprobar si el producto existe en la tabla producto
    $checkProduct = $conn->prepare("SELECT COUNT(*) FROM producto WHERE id_producto = ?");
    $checkProduct->bind_param("i", $id_producto);
    $checkProduct->execute();
    $checkProduct->bind_result($productExists);
    $checkProduct->fetch();

    if ($productExists > 0) {
        // Insertar en la tabla detalle_compra
        $stmt = $conn->prepare("INSERT INTO detalle_compra (id_producto, cantidad, precio) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $id_producto, $cantidad, $precio);
        $stmt->execute();
        echo "Producto añadido al detalle de compra.";
    } else {
        echo "El producto no existe en la base de datos.";
    }
}
?>
