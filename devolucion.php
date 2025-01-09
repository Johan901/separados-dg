<?php
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
    <title>Panel de Administración - DG</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/styles_agregar_pedidos.css?v=2.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        .section-title {
            margin: 20px 0; /* Espaciado mejorado para los títulos de sección */
            font-weight: bold;
            font-size: 1.2em;
        }
        .input-group {
            margin: 10px 0; /* Espaciado para los grupos de entrada */
        }
    </style>
</head>

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
        </div>
    </div>
    
    <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
    <a href="logout.php" class="logout-button">Cerrar Sesión</a>
</header>

<body>
    <div class="container">

        <!-- Búsqueda de Referencia -->
        <label for="referencia-busqueda">Buscar Referencia:</label>
        <div class="input-group">
            <input type="text" id="referencia-busqueda" name="referencia" placeholder="Ingrese la referencia">
            <button class="boton-buscar" type="button" onclick="buscarReferencia()">Buscar</button>
        </div>
        <label for="color">Color:</label>
        <select id="color" name="color">
            <option>Seleccione un color</option>
        </select>

        <label for="tipo-prenda">Tipo de Prenda:</label>
        <input type="text" id="tipo-prenda" name="tipo-prenda" readonly>

        <label for="">Cantidad:</label>
        <input type="text" id="cantidad" name="cantidad" placeholder="Ingrese cantidad" onchange="calcularSubtotal()" required>

        <!-- Campo de Observaciones -->
        <label for="observacion">Observación:</label>
        <textarea id="observacion" name="observacion" placeholder="Ingrese la observación" required></textarea>

        <button class="boton-buscar" onclick="agregarDevolucion()">Agregar Devolucion</button>

     </div>

    <script src="js/main_user.js?v=1.1"></script>
    <script src="devolucion_script.js?v=9.0" defer></script>

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
