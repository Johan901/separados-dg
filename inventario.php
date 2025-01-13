<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sabor Tostado</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=2.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <!-- Menú Hamburguesa -->
        <div class="hamburger-menu">
             <i class="fas fa-bars"></i>
        </div>
        <div class="dropdown-menu">
            <a href="admin_panel.php">Inicio</a>
            <a href="agregar_usuario.php">Agregar nuevo cliente</a>
            <a href="info_cliente.php">Ver clientes</a>
            <a href="inventario.php">Inventario de productos</a>
            <a href="nuevo_pedido.php">Agregar nuevo pedido</a>
            <a href="historial_pedidos.php">Historial de pedidos</a>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <div class="header-right">
            <form action="inventario.php" method="GET" class="search-form">
                <h2><label for="ref">Buscar prenda por referencia:</label></h2>
                <input type="text" name="ref" required>
                <input type="submit" value="Buscar">
            </form>
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <h2>Gestión de Inventario</h2>

    <?php
    include('config.php');

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.html');
        exit();
    }

    if (isset($_GET['ref']) && !empty($_GET['ref'])) {
        $ref = $_GET['ref'];
        $query = "SELECT * FROM inventario WHERE ref = :ref";
        $stmt = $conn->prepare($query);
        $stmt->execute(['ref' => $ref]);

        if ($stmt->rowCount() > 0) {
            echo "<h2>Resultados de la búsqueda</h2>";
        } else {
            echo "No se encontraron prendas con la referencia: $ref";
        }
    } else {
        $query = "SELECT * FROM inventario";
        $stmt = $conn->query($query);
        echo "<h2>Stock de Prendas</h2>";
    }

    echo "<table>
        <tr>
            <th>Referencia</th>
            <th>Tipo de Prenda</th>
            <th>Color</th>
            <th>Cantidad</th>
            <th>Precio al Detal (COP)</th>
            <th>Precio por Mayor (COP)</th>
            <th>Acciones</th>
        </tr>";

    while ($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $precio_al_detal_formateado = '$' . number_format($producto['precio_al_detal'], 0, ',', '.');
        $precio_por_mayor_formateado = '$' . number_format($producto['precio_por_mayor'], 0, ',', '.');

        echo "<tr>
            <td>{$producto['ref']}</td>
            <td>{$producto['tipo_prenda']}</td>
            <td>{$producto['color']}</td>
            <td>{$producto['cantidad']}</td>
            <td>{$precio_al_detal_formateado}</td>
            <td>{$precio_por_mayor_formateado}</td>
            <td><a href='agregar_cantidad_bodeguero.php?ref={$producto['ref']}&color={$producto['color']}' class='button'>Agregar Cantidad</a></td>
        </tr>";
    }
    echo "</table>";
    ?>

<script src="js/main_user.js?v=2.1"></script>
</body>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h2 class="footer-title">Sobre Nosotros</h2>
            <p>Somos una empresa comprometida en brindar el mejor servicio a nuestros clientes. Contáctanos para más información.</p>
        </div>
        <div class="footer-section links">
            <h2 class="footer-title">Enlaces Rápidos</h2>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Servicios</a></li>
                <li><a href="#">Sobre Nosotros</a></li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </div>
        <div class="footer-section contact-form">
            <h2 class="footer-title">Contáctanos</h2>
            <p>Email: info@dulceguadalupe.com</p>
            <p>Teléfono: +57 3153925613</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 Dulce Guadalupe | Todos los derechos reservados
    </div>
</footer>

</html>