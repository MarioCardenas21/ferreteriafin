<?php
include 'conexion.php';

$id_producto = $_GET['id_producto'] ?? null;
if (!$id_producto) {
    die("ID de producto no proporcionado.");
}

// Obtener datos del producto
$stmt = $conn->prepare("SELECT nombre, id_categoria, precio, stock FROM producto WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$stmt->bind_result($nombre, $id_categoria, $precio, $stock);
if (!$stmt->fetch()) {
    die("Producto no encontrado.");
}
$stmt->close();

// Obtener categorías
$categorias = $conn->query("SELECT id_categoria, nombre FROM categoria");

// Guardar cambios si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = $_POST['nombre'];
    $nueva_categoria = $_POST['id_categoria'] ?: null;
    $nuevo_precio = $_POST['precio'];
    $nuevo_stock = $_POST['stock'];

    $stmt = $conn->prepare("UPDATE producto SET nombre = ?, id_categoria = ?, precio = ?, stock = ? WHERE id_producto = ?");
    $stmt->bind_param("siddi", $nuevo_nombre, $nueva_categoria, $nuevo_precio, $nuevo_stock, $id_producto);
    $stmt->execute();
    header("Location: inventarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
</head>
<body>
    <h1>Editar Producto</h1>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre); ?>" required><br>

        <label>Categoría:</label>
        <select name="id_categoria">
            <option value="">Sin categoría</option>
            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <option value="<?= $cat['id_categoria']; ?>" <?= ($cat['id_categoria'] == $id_categoria) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label>Precio:</label>
        <input type="number" step="0.01" name="precio" value="<?= $precio; ?>" required><br>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $stock; ?>" required><br>

        <button type="submit">Guardar Cambios</button>
        <a href="inventarios.php">Cancelar</a>
    </form>
</body>
</html>

<?php $conn->close(); ?>
