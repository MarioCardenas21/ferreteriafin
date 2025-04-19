<?php
include 'conexion.php';

// Verificar si la solicitud es AJAX antes de cargar HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json');
    $accion = $_POST['accion'];
    $response = ["success" => false, "message" => ""];

    if ($accion === "crear") {
        $nombre = trim($_POST['nombre']);
        if (!empty($nombre)) {
            $query = "INSERT INTO categoria (nombre) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $nombre);
            if ($stmt->execute()) {
                $response = ["success" => true, "message" => "Categoría creada con éxito."];
            } else {
                $response = ["success" => false, "message" => "Error al crear la categoría."];
            }
            $stmt->close();
        }
    }

    if ($accion === "modificar") {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        if ($id > 0 && !empty($nombre)) {
            $query = "UPDATE categoria SET nombre = ? WHERE id_categoria = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $nombre, $id);
            if ($stmt->execute()) {
                $response = ["success" => true, "message" => "Categoría modificada con éxito."];
            } else {
                $response = ["success" => false, "message" => "Error al modificar la categoría."];
            }
            $stmt->close();
        }
    }

    if ($accion === "eliminar") {
        $id = intval($_POST['id']);
        if ($id > 0) {
            $query = "DELETE FROM categoria WHERE id_categoria = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $response = ["success" => true, "message" => "Categoría eliminada con éxito."];
            } else {
                $response = ["success" => false, "message" => "Error al eliminar la categoría."];
            }
            $stmt->close();
        }
    }

    echo json_encode($response);
    $conn->close();
    exit;
}

// Obtener categorías
$query_categorias = "SELECT id_categoria, nombre FROM categoria";
$result_categorias = $conn->query($query_categorias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="estilos_categorias.css">
</head>
<body>

    <header>
        <h1>Gestión de Categorías</h1>
    </header>

    <main>

        <section class="formulario">
            <h2>Crear Categoría</h2>
            <form id="formCrear">
                <input type="text" name="nombre" id="nombreCrear" placeholder="Nombre de la categoría" required>
                <button type="submit">Crear</button>
            </form>
            <p id="mensajeCrear" style="display: none; color: green;"></p>
        </section>

        <section class="categorias">
            <h2>Lista de Categorías</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaCategorias">
                    <?php while ($row = $result_categorias->fetch_assoc()): ?>
                        <tr id="categoria-<?= $row['id_categoria']; ?>">
                            <td><?= $row['id_categoria']; ?></td>
                            <td><?= htmlspecialchars($row['nombre']); ?></td>
                            <td>
                                <button onclick="mostrarFormularioModificar(<?= $row['id_categoria']; ?>, '<?= htmlspecialchars($row['nombre']); ?>')">Modificar</button>
                                <button onclick="eliminarCategoria(<?= $row['id_categoria']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section class="formulario-modificar" id="formulario-modificar" style="display: none;">
            <h2>Modificar Categoría</h2>
            <form id="formModificar">
                <input type="hidden" name="id" id="id">
                <input type="text" name="nombre" id="nombreModificar" placeholder="Nuevo nombre de la categoría" required>
                <button type="submit">Modificar</button>
                <button type="button" onclick="cerrarFormulario()">Cancelar</button>
            </form>
            <p id="mensajeModificar" style="display: none; color: green;"></p>
        </section>

    </main>

    <footer>
        <p>&copy; 2024 Ferretería. Todos los derechos reservados.</p>
    </footer>

    <script>
        function fetchData(accion, formData) {
            return fetch("categorias.php", {
                method: "POST",
                body: formData
            }).then(response => response.json());
        }

        document.getElementById("formCrear").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            formData.append("accion", "crear");

            fetchData("crear", formData).then(data => {
                alert(data.message);
                if (data.success) location.reload();
            }).catch(error => console.error("Error AJAX (Crear):", error));
        });

        function mostrarFormularioModificar(id, nombre) {
            document.getElementById('formulario-modificar').style.display = 'block';
            document.getElementById('id').value = id;
            document.getElementById('nombreModificar').value = nombre;
        }

        function cerrarFormulario() {
            document.getElementById('formulario-modificar').style.display = 'none';
        }

        document.getElementById("formModificar").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            formData.append("accion", "modificar");

            fetchData("modificar", formData).then(data => {
                alert(data.message);
                if (data.success) location.reload();
            }).catch(error => console.error("Error AJAX (Modificar):", error));
        });

        function eliminarCategoria(id) {
            if (confirm("¿Seguro que quieres eliminar esta categoría?")) {
                let formData = new FormData();
                formData.append("accion", "eliminar");
                formData.append("id", id);

                fetchData("eliminar", formData).then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                }).catch(error => console.error("Error AJAX (Eliminar):", error));
            }
        }
    </script>
      <div class="form-actions">
        <a href="panel.php" class="button">Regresar al Panel</a>

</body>
</html>

<?php
$conn->close();
?>

