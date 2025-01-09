<?php
// Incluye la conexión a la base de datos
include('config.php');

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Devoluciones</title>
    <link rel="stylesheet" href="css/styles_admin_panel.css?v=1.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
            <div class="dropdown-menu">
                <a href="admin_panel.php">Inicio</a>
                <a href="agregar_usuario.php">Agregar nuevo cliente</a>
                <a href="info_cliente.php">Ver clientes</a>
                <a href="inventario.php">Inventario de productos</a>
                <a href="nuevo_pedido.php">Agregar nuevo pedido</a>
                <a href="historial_pedidos.php">Historial de pedidos</a>
                <a href="devolucion.php">Devoluciones</a>
            </div>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <div class="header-right">
            <form action="devoluciones.php" method="GET" class="search-form">
                <h2><label for="buscar">Buscar devolución por referencia o color:</label></h2>
                <input type="text" name="buscar" required>
                <input type="submit" value="Buscar">
            </form>
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="titu1">
        <h2>Gestión de Devoluciones</h2>
    </div>

    <?php
    // Consulta de devoluciones
    $query = "SELECT * FROM devoluciones";

    // Verificar si se ha enviado un parámetro de búsqueda
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = $_GET['buscar'];

        // Query para buscar devoluciones por referencia o color
        $query .= " WHERE LOWER(ref) LIKE LOWER(:buscar) OR LOWER(color) LIKE LOWER(:buscar)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':buscar', '%' . $buscar . '%');
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo '<div class="titu1">';
            echo "<h2>Resultados de la búsqueda para '$buscar'</h2>";
            echo '</div>';
        } else {
            echo '<div class="titu1">';
            echo "No se encontraron devoluciones con la referencia o color: $buscar";
            echo '</div>';
        }
    } else {
        $stmt = $conn->query($query);
        echo '<div class="titu1">';
        echo '<h2>Lista de Devoluciones</h2>';
        echo '</div>';
    }

    // Mostrar las devoluciones en una tabla
    echo "<table border='1'>
        <tr>
            <th>Referencia</th>
            <th>Color</th>
            <th>Cantidad</th>
            <th>Observaciones</th>
        </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ref']) . "</td>";
        echo "<td>" . htmlspecialchars($row['color']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
        echo "<td>" . htmlspecialchars($row['obvs']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

   
<script src="js/main_user.js?v=1.1"></script>
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
         &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de Devoluciones e Inventario.
    </div>
</footer>

</html>
