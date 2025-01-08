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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="js/main_user.js?v=3.0"></script>
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
        <h1>Reporte de Pedidos Separados por Vencer Esta Semana</h1>

        <form id="buscar-vencimiento" method="POST">
            <button type="submit">Buscar Pedidos Separados por Vencer Esta Semana</button>
        </form>

        <div id="reporte-vencimiento" class="reporte-container"></div>

        <!-- Tabla para mostrar los pedidos -->
        <div id="tabla-vencimiento" style="display:none; margin-top: 20px;">
            <h2>Pedidos a Vencer</h2>
            <table id="tabla-pedidos-vencer" border="1" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha Pedido</th>
                        <th>Total Pedido</th>
                        <th>Asesor</th>
                        <th>Envio</th>
                        <th>Fecha Limite</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Cédula Cliente</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Llamada Ajax al servidor para verificar los pedidos a vencer
            $('#buscar-vencimiento').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'vencimiento_back.php', // URL al archivo PHP
                    type: 'POST',
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.pedidos.length > 0) {
                            let tableRows = '';
                            data.pedidos.forEach(item => {
                                tableRows += `
                                    <tr>
                                        <td>${item.id_pedido}</td>
                                        <td>${item.fecha_pedido}</td>
                                        <td>${item.total_pedido}</td>
                                        <td>${item.asesor}</td>
                                        <td>${item.envio}</td>
                                        <td>${item.fecha_limite}</td>
                                        <td>${item.estado}</td>
                                        <td>${item.cliente_nombre}</td>
                                        <td>${item.cliente_cedula}</td>
                                    </tr>
                                `;
                            });

                            // Mostrar tabla con los pedidos a vencer
                            $('#tabla-vencimiento').show();
                            $('#tabla-pedidos-vencer tbody').html(tableRows);
                        } else {
                            Swal.fire({
                                title: 'No hay pedidos por vencer',
                                text: 'No hay pedidos que venzan en la próxima semana.',
                                icon: 'info',
                                confirmButtonText: 'Cerrar'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al obtener los pedidos.',
                            icon: 'error',
                            confirmButtonText: 'Cerrar'
                        });
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
