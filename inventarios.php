<?php
include 'conexion.php';

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Variables de búsqueda
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$category_filter = isset($_POST['category_filter']) ? intval($_POST['category_filter']) : '';

// Obtener categorías
$query_categories = "SELECT id_categoria, nombre FROM categoria";
$result_categories = $conn->query($query_categories);
if (!$result_categories) {
    die("Error al obtener categorías: " . $conn->error);
}

// Consulta segura de productos activos (con LEFT JOIN correcto)
$query = "
    SELECT p.id_producto, p.nombre, p.id_categoria, 
           p.stock AS cantidad_disponible, 
           c.nombre AS nombre_categoria, 
           p.precio,
           p.Precio_compra
    FROM producto p
    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
    WHERE p.activo = 1 AND p.nombre LIKE ? 
";

if (!empty($category_filter)) {
    $query .= " AND p.id_categoria = ? ";
}

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$search_param = "%{$search}%";

if (!empty($category_filter)) {
    $stmt->bind_param("si", $search_param, $category_filter);
} else {
    $stmt->bind_param("s", $search_param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventarios</title>
    <link rel="stylesheet" href="estilos_inventario.css">
</head>
<body>
    <header>
        <h1>Inventario de Productos</h1>
        <h2>Ferretería</h2>
    </header>

    <main>
        <form method="POST" action="inventarios.php">
            <div class="filter-container">
                <input type="text" name="search" placeholder="Buscar producto..." 
                       value="<?= htmlspecialchars($search); ?>" class="search-box">
                <select name="category_filter" class="category-select">
                    <option value="">Seleccionar categoría</option>
                    <?php while ($category = $result_categories->fetch_assoc()): ?>
                        <option value="<?= $category['id_categoria']; ?>" 
                            <?= $category['id_categoria'] == $category_filter ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="filter-button">Filtrar</button>
            </div>
        </form>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Cantidad Disponible</th>
                    <th>Precio</th>
                    <th>Precio_compra</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']); ?></td>
                            <td><?= htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                            <td><?= $row['cantidad_disponible']; ?></td>
                            <td>Q<?= number_format($row['precio'], 2, ',', '.'); ?></td>
                            <td>Q<?= number_format($row['Precio_compra'] ?? 0, 2, ',', '.'); ?></td>

                            <td>
                                <a href="editar_producto.php?id_producto=<?= $row['id_producto']; ?>" 
                                   class="edit-button">Editar</a>
                                <a href="eliminar_producto.php?id_producto=<?= $row['id_producto']; ?>" 
                                   class="delete-button" 
                                   onclick="return confirm('¿Seguro que quieres ocultar este producto del inventario?');">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No se encontraron productos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <a href="panel.php" class="button">Volver al Menú Principal</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
