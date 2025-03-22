<?php
session_start();
if (isset($_SESSION['nombre_usuario'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
} else {
    // Redirigir al login si no está autenticado
    header('Location: index.html');
    exit();
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_admin_panel.css?v=3.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .sections {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }
        .section {
            flex: 1;
            margin: 0 10px;
            padding: 35px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: background-color 0.3s;
            position: relative;
        }
        .section:hover {
            background-color: #f1f1f1;
        }
        .dropdown {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%; /* Asegura que el menú sea del mismo ancho que el panel */
            z-index: 1;
            top: 100%; /* Posición del menú justo debajo del panel */
            left: 0;
            margin-top: 0px; /* Añade margen superior para separar del footer */
}
        .section:hover .dropdown,
        .dropdown:hover {
            display: block;
        }
        .dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s;
        }
        .dropdown a:hover {
            background-color: #e91d29;
            color: white;
        }
      
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger-menu">
             <i class="fas fa-bars"></i>
        </div>
        <div class="dropdown-menu">
                <a href="bodeguero_panel.php">Inicio</a>
                <a href="agregar_inventario_bodeguero.php">Agregar Inventario</a>
                <a href="inventario_bodeguero.php">Inventario de productos</a>
                <a href="historial_bodeguero.php">Historial de pedidos</a>
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>


    <!-- Mostrar el mensaje de bienvenida -->
    <div style="text-align: center; padding: 20px; background-color: #e0f7fa; font-size: 24px;">
        <?php echo "Bienvenid@ " . htmlspecialchars($nombre_usuario) . " al sistema Sistema de Gestión de Separados e Inventario de Dulce Guadalupe, hoy será un día grandioso."; ?>
    </div>

    <h2 style="text-align:center;">SISTEMA DE GESTIÓN DE SEPARADOS E INVENTARIO</h2>
    <h2 style="text-align:center;">Dulce Guadalupe</h2>

    <div class="sections">

    <div class="section" style="background-color: #b2dfdb;">
            <h3>INVENTARIO</h3>
            <div class="dropdown">
                <a href="inventario_bodeguero.php">Inventario de Productos</a>
                <a href="agregar_inventario_bodeguero.php">Agregar Inventario</a>
            </div>
        </div>

        <div class="section" style="background-color:rgb(103, 164, 119);">
            <h3>OBSERVACIONES</h3>
            <div class="dropdown">
                <a href="observaciones_bodeguero.php">Ver todas las observaciones</a>
            </div>
        </div>

        <div class="section" style="background-color: #ffe0b2;">
            <h3>PEDIDOS</h3>
            <div class="dropdown">
                <a href="historial_bodeguero.php">Historial de Pedidos</a>
            </div>
        </div>
    </div>


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
        &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de separodos e Inventario.
    </div>        
    </footer>



</html>
