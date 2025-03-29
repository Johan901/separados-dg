<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observaciones de Pedidos</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=2.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;400;600&display=swap">
</head>
<body>
    <header>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <h2>Observaciones de Pedidos</h2>

    <?php
    include('config.php');
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.html');
        exit();
    }

    $query = "SELECT id_pedido, asesor, observaciones, fecha FROM pedidos WHERE observaciones IS NOT NULL AND observaciones <> '' ORDER BY fecha DESC";
    $stmt = $conn->query($query);

    echo "<table>
        <tr>
            <th>ID Pedido</th>
            <th>Asesor</th>
            <th>Observaciones</th>
        </tr>";

    while ($pedido = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
            <td>{$pedido['id_pedido']}</td>
            <td>{$pedido['asesor']}</td>
            <td>{$pedido['observaciones']}</td>
        </tr>";
    }
    echo "</table>";
    ?>
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
        &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de separodos e Inventario.
    </div>
</footer>

</html>
