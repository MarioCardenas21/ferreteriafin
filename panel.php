<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="estilos_panel.css">
    <script>
        function confirmLogout() {
            return confirm("¿Estás seguro de que deseas cerrar sesión?");
        }
    </script>
</head>
<body>
    <header class="header">
        <h1>Bienvenido al Panel de Administración</h1>
        <h2>Ferretería Baruch</h2>
    </header>
    <main class="main-container">
        <div class="button-container">
            <a href="agregar_producto.php" class="button">Agregar Producto</a>
            <a href="consultar_ventas.php" class="button">Consultar Ventas</a>
            <a href="categorias.php" class="button">Categorías</a>
            <a href="hacer_venta.php" class="button">Hacer Venta</a>
            <a href="proveedor.php" class="button">Provedores</a>
            <a href="gestor_clientes.php" class="button">Gestión Clientes</a>
            <a href="inventarios.php" class="button">Inventarios</a>
            <a href="logout.php" class="button logout" onclick="return confirmLogout()">Cerrar Sesión</a>
        </div>
    </main>
    
</body>
</html>
