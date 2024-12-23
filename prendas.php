<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Reportes - DG</title>
    <link rel="stylesheet" href="css/styles_reportes.css?v=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
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
        
        <!-- Formulario para obtener prendas agotadas -->
        <form id="filtro-asesor">
            <button type="submit">Buscar si hay alguna prenda agotada</button>
        </form>

        <!-- Sección de reporte de prendas agotadas -->
        <div id="reporte" class="reporte-container"></div>

        <!-- Tabla para mostrar las prendas agotadas -->
        <div id="tabla-agotadas" style="display:none; margin-top: 20px;">
            <h2>Prendas Agotadas</h2>
            <table id="tabla-prendas-agotadas" border="1" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Referencia</th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Sección para buscar cantidad por referencia -->
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
            // Llamada Ajax para obtener prendas agotadas
            $.ajax({
                url: 'prendas_back.php',
                type: 'POST',
                success: function (response) {
    try {
        const data = typeof response === "string" ? JSON.parse(response) : response; // Solo parsear si es una cadena
        console.log(data); // Depuración para ver cómo está llegando la respuesta

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

            // Mostrar la tabla con las prendas agotadas
            $('#tabla-agotadas').show(); // Asegurarse de que la tabla se muestre
            $('#tabla-prendas-agotadas tbody').html(tableRows); // Llenar la tabla con los datos
        } else {
            Swal.fire({
                title: 'No hay prendas agotadas',
                text: 'No se han encontrado prendas agotadas en el inventario.',
                icon: 'info',
                confirmButtonText: 'Cerrar'
            });
            $('#tabla-agotadas').hide(); // Asegurarse de ocultar la tabla si no hay datos
        }
    } catch (e) {
        Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al procesar los datos de la respuesta.',
            icon: 'error',
            confirmButtonText: 'Cerrar'
        });
        console.error("Error al parsear JSON:", e);
    }
},
                error: function () {
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al verificar el inventario.',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            });

            // Llamada Ajax para buscar la cantidad por referencia
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
                        const data = JSON.parse(response);
                        if (data.prendas.length > 0) {
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
                    },
                    error: function () {
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error al buscar la referencia.',
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
        &copy; 2024 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de Inventarios.
    </div>
</footer>
</html>
