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
                <a href="reportes.php">Reportes</a>
            </div>
        </div>
        
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="container">
        <h1>Reporte de Prendas Agotadas</h1>
        
        <!-- Botón para buscar prendas agotadas -->
        <button class="button" id="btn-buscar-agotadas" type="button">Mostrar prendas agotadas</button>

        <!-- Sección de reporte de prendas agotadas -->
        <div id="reporte" class="reporte-container"></div>

        <table id="tabla-prendas-agotadas" border="1" style="width: 100%; border-collapse: collapse; display: none;">
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Color</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se llenan dinámicamente -->
            </tbody>
        </table>

        <div id="search-container" style="margin-top: 40px;">
            <h2>Buscar Cantidad por Prenda</h2>
            <form id="search-form" method="POST">
                <label for="search-ref">Referencia:</label>
                <input type="text" id="search-ref" name="search-ref" placeholder="Buscar por referencia">
                <button type="submit">Buscar</button>
            </form>

            <!-- Resultados de la búsqueda de prendas por referencia -->
            <div id="search-results" style="margin-top: 20px;"></div>
        </div>
    </div>

    <script>
$(document).ready(function () {
    // Manejar clic en el botón para buscar prendas agotadas
    $('#btn-buscar-agotadas').on('click', function () {
        $.ajax({
            url: 'prendas_back.php',
            type: 'POST',
            data: { action: 'fetch_agotadas' },
            success: function (response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.agotadas && data.agotadas.length > 0) {
                        let tableRows = '';
                        data.agotadas.forEach(item => {
                            tableRows += `
                                <tr>
                                    <td>${item.ref}</td>
                                    <td>${item.color}</td>
                                </tr>
                            `;
                        });
                        $('#tabla-prendas-agotadas tbody').html(tableRows);
                        $('#tabla-prendas-agotadas').show();
                    } else {
                        Swal.fire({
                            title: 'Todo en stock',
                            text: 'No hay prendas agotadas en el inventario.',
                            icon: 'success',
                            confirmButtonText: 'Cerrar'
                        });
                        $('#tabla-prendas-agotadas').hide();
                    }
                } catch (error) {
                    console.error('Error al procesar los datos:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la respuesta del servidor.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo establecer conexión con el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });

    // Manejar búsqueda por referencia
    $('#search-form').on('submit', function (e) {
        e.preventDefault();

        const ref = $('#search-ref').val();

        if (ref === '') {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, ingrese una referencia.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
            return;
        }

        $.ajax({
            url: 'prendas_back.php',
            type: 'POST',
            data: { search_ref: ref },
            success: function (response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.prendas && data.prendas.length > 0) {
                        let tableRows = '<table border="1" style="width: 100%; border-collapse: collapse;"><thead><tr><th>Color</th><th>Cantidad</th></tr></thead><tbody>';
                        data.prendas.forEach(item => {
                            tableRows += `
                                <tr>
                                    <td>${item.color}</td>
                                    <td>${item.cantidad}</td>
                                </tr>
                            `;
                        });
                        tableRows += '</tbody></table>';
                        $('#search-results').html(tableRows);
                    } else {
                        $('#search-results').html('<p>No se encontraron resultados para la referencia buscada.</p>');
                    }
                } catch (error) {
                    console.error('Error al procesar los datos:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la respuesta del servidor.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo establecer conexión con el servidor.',
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
            border-color: #800020; /* Cambia el color del borde al enfocar */
            outline: none; /* Elimina el borde de enfoque predeterminado */
        }

        /* Estilos para el botón de búsqueda */
        #search-form button {
            background-color: #800020;
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
        background-color: #800020;
        color: white;
        border: none;
        border-radius: 5px;
    }

    .report-box button:hover {
        background-color: #800020;
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
