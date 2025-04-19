<?php
// Incluir el archivo de conexión
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se han recibido todos los campos necesarios
    if (!empty($_POST['nombre']) && !empty($_POST['precio']) && !empty($_POST['descripcion']) && !empty($_POST['stock']) && !empty($_POST['material'])) {
        
        // Sanitizar los datos del formulario para evitar inyecciones y errores
        $nombre = $conn->real_escape_string(trim($_POST['nombre']));
        $precio = floatval($_POST['precio']);
        $descripcion = $conn->real_escape_string(trim($_POST['descripcion']));
        $stock = intval($_POST['stock']);
        $material = "Material " . $conn->real_escape_string(trim($_POST['material']));

        // Verificar si se seleccionó una categoría y asignar NULL si no se seleccionó ninguna
        $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : NULL;

        // Validar y obtener la cantidad de medidas y la unidad
        if (!empty($_POST['medidas']) && intval($_POST['medidas']) > 0) {
            $medidas = $_POST['medidas'] . " " . $_POST['unidad_medida'];
        } else {
            $medidas = ''; // Si no se ingresa una cantidad válida, no se agrega medidas a la descripción
        }

        // Concatenar material y medidas a la descripción
        $descripcion = $material . " " . $medidas . " " . $descripcion;

        // Preparar la consulta de inserción
        $stmt = $conn->prepare("INSERT INTO producto (nombre_producto, precio_unitario, id_categoria, descripcion, stock) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sdiss", $nombre, $precio, $id_categoria, $descripcion, $stock);

            // Ejecutar la consulta y verificar si fue exitosa
            if ($stmt->execute()) {
                echo "<script>alert('Nuevo producto agregado exitosamente.'); window.location.href = 'panel.php';</script>";
            } else {
                echo "<script>alert('Error al agregar el producto: " . $stmt->error . "');</script>";
            }

            // Cerrar la consulta preparada
            $stmt->close();
        } else {
            echo "<script>alert('Error al preparar la consulta: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Todos los campos son obligatorios.');</script>";
    }
}

// Obtener las categorías desde la base de datos
$query_categorias = "SELECT id_categoria, nombre_categoria FROM categoria";
$result_categorias = $conn->query($query_categorias);
?>
