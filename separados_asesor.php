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
    <link rel="stylesheet" href="css/styles_reportes.css?v=6.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="js/main_user.js?v=3.0"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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
                <a href="reportes.php">Reportes</a>
            </div>
        </div>
        
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="container">
        <h1>Reporte de Separados por Línea</h1>
        
        <form id="filtro-asesor" method="POST" action="">
            <label for="asesor">Línea</label>
            <select id="asesor" name="asesor">
                <option value="">Seleccione una línea</option>
                <option value="3104238002">3104238002</option>
                <option value="3147363095">3147363095</option>
                <option value="3155081961">3155081961</option>
            </select>

            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio">

            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin">

            <button type="submit">Generar Reporte</button>
        </form>

        <div id="reporte" class="reporte-container"></div>

        <!-- Nuevo Div con estilos -->
        <div class="report-box">
            <h1>Reporte de Separados de Líneas en Conjunto</h1>

            <form id="filtro-asesor-conjunto" method="POST" action="">
                <label for="fecha_inicio_conjunto">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio_conjunto" id="fecha_inicio_conjunto">

                <label for="fecha_fin_conjunto">Fecha Fin:</label>
                <input type="date" name="fecha_fin_conjunto" id="fecha_fin_conjunto">

                <button type="submit">Generar Reporte</button>
            </form>

            <div id="reporte_conjunto"></div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        // Reporte por Asesor
        $('#filtro-asesor').on('submit', function (e) {
            e.preventDefault();
            const asesor = $('#asesor').val();
            const fecha_inicio = $('#fecha_inicio').val();
            const fecha_fin = $('#fecha_fin').val();

            if (!asesor || !fecha_inicio || !fecha_fin) {
                alert("Por favor, complete todos los campos.");
                return;
            }

            $.ajax({
                url: 'separados_individual.php',
                type: 'POST',
                data: { asesor, fecha_inicio, fecha_fin },
                success: function (response) {
                    $('#reporte').html(response);
                },
                error: function () {
                    alert("Ocurrió un error al generar el reporte.");
                }
            });
        });
        
        // Reporte Conjunto
        $('#filtro-asesor-conjunto').on('submit', function (e) {
            e.preventDefault();
            const fecha_inicio_conjunto = $('#fecha_inicio_conjunto').val();
            const fecha_fin_conjunto = $('#fecha_fin_conjunto').val();

            if (!fecha_inicio_conjunto || !fecha_fin_conjunto) {
                alert("Por favor, complete todos los campos.");
                return;
            }

            $.ajax({
                url: 'separados_conjunto.php',
                type: 'POST',
                data: { fecha_inicio_conjunto, fecha_fin_conjunto },
                success: function (response) {
                    $('#reporte_conjunto').html(response);
                },
                error: function () {
                    alert("Ocurrió un error al generar el reporte conjunto.");
                }
            });
        });
    });

</script>
</body>
<style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        .report-box {
            margin-bottom: 30px; /* Espacio entre los cuadros */
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
        &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de separodos e Inventario.
    </div>        
    </footer>

</html>
