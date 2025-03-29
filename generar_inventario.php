<?php
session_start();
include('config.php');

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
    <title>Panel de Reportes - DG</title>
    <link rel="stylesheet" href="css/styles_reportes.css?v=5.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="js/main_user.js?v=3.0"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
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

    <div class="container">
    <h1>Generar Inventario Disponible</h1>
    <button class="button" id="btn-inventario">Generar inventario</button>

    <h1>Generar Separados Actuales</h1>
    <button class="button" id="btn-separados">Generar separados</button>
</div>

<script>
$(document).ready(function () {
    $('#btn-inventario').on('click', function () {
        generarExcel('inventario_excel.php', 'Inventario descargado con éxito');
    });

    $('#btn-separados').on('click', function () {
        generarExcel('separados_excel.php', 'Separados descargados con éxito');
    });

    function generarExcel(url, mensaje) {
        // Crear un iframe temporal para manejar la descarga sin afectar la página
        var iframe = document.createElement("iframe");
        iframe.style.display = "none";
        iframe.src = url;
        document.body.appendChild(iframe);

        // Esperar unos segundos antes de mostrar la alerta
        setTimeout(function () {
            swal("Éxito", mensaje, "success");
            document.body.removeChild(iframe);
        }, 3000); // Ajusta el tiempo si es necesario
    }
});
</script>



</body>

<style>
        /* Estilos para el campo de búsqueda */
        #search-ref {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
            border: 2px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        #search-ref:focus {
            border-color: #e91d29; /* Cambia el color del borde al enfocar */
            outline: none; /* Elimina el borde de enfoque predeterminado */
        }

        /* Estilos para el botón de búsqueda */
        #search-form button {
            background-color: #e91d29;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        #search-form button:hover {
            background-color:rgb(159, 39, 39); /* Cambia el color de fondo al pasar el mouse */
        }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
    }

    .report-box {
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .report-box h1 {
        margin-bottom: 20px;
    }

    .report-box form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .report-box input, .report-box select {
        padding: 10px;
        font-size: 16px;
        width: 100%;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .report-box button {
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
        background-color: #e91d29;
        color: white;
        border: none;
        border-radius: 5px;
    }

    .report-box button:hover {
        background-color: #e91d29;
    }

    #reporte, #reporte_conjunto {
        margin-top: 20px;
        padding: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>

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
        &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de Inventarios.
    </div>
</footer>
</html>
