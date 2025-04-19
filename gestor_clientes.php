<?php
include 'conexion.php';

$mensaje = "";

// Agregar cliente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nombre_cliente']) && !empty($_POST['telefono_cliente'])) {
        $nombre_cliente = trim($_POST['nombre_cliente']);
        $telefono_cliente = trim($_POST['telefono_cliente']);

        $stmt = $conn->prepare("INSERT INTO cliente (nombre, telefono) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $nombre_cliente, $telefono_cliente);
            if ($stmt->execute()) {
                $mensaje = "✅ Cliente agregado exitosamente.";
            } else {
                $mensaje = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
}

// Consultar clientes
$query_clientes = "SELECT id_cliente, nombre, telefono FROM cliente WHERE activo = 1";

$result_clientes = $conn->query($query_clientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
        }
        table th, table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Gestor de Clientes</h1>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-header">➕ Agregar Cliente</div>
        <div class="card-body">
            <form method="POST" action="gestor_clientes.php">
                <div class="mb-3">
                    <label for="nombre_cliente" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" name="nombre_cliente" id="nombre_cliente" required>
                </div>
                <div class="mb-3">
                    <label for="telefono_cliente" class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono_cliente" id="telefono_cliente" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cliente</button>
            </form>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="card">
        <div class="card-header">📋 Lista de Clientes</div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_clientes->num_rows > 0): ?>
                    <?php while ($row = $result_clientes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_cliente']; ?></td>
                            <td><?= htmlspecialchars($row['nombre']); ?></td>
                            <td><?= htmlspecialchars($row['telefono']); ?></td>
                            <td>
                                <a href="editar_cliente.php?id=<?= $row['id_cliente']; ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                                <a href="eliminar_cliente.php?id=<?= $row['id_cliente']; ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar este cliente?');">🗑 Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay clientes registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Regresar -->
    <div class="text-center mt-4">
        <a href="panel.php" class="btn btn-secondary">🔙 Regresar al Panel</a>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
