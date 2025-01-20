<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Reportes - DG</title>
    <link rel="stylesheet" href="css/styles_reportes.css?v=5.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
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
                <a href="admin_panel.php">Reportes</a>
            </div>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="container">
        <h1>Reportes de Ventas</h1>

        <label for="dia">Seleccionar día:</label>
        <input type="date" id="dia" name="dia" placeholder="dd/mm/aaaa">

        <label for="mes">Seleccionar mes:</label>
        <input type="month" id="mes" name="mes" placeholder="enero de 2025">

        <!-- Botón para generar los reportes -->
        <button id="btn-generar-reporte" class="button" type="button">Generar Reporte</button>

        <h2>Referencias más vendidas</h2>
        <table id="tabla-referencias" border="1" style="width: 100%; border-collapse: collapse; display: none;">
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Color</th>
                    <th>Cantidad Vendida</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se llenan dinámicamente con AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            // Evento para generar el reporte
            $('#btn-generar-reporte').on('click', function () {
    var dia = $('#dia').val();
    var mes = $('#mes').val();

    console.log("Día seleccionado:", dia);
    console.log("Mes seleccionado:", mes);

    // Validar que al menos uno de los campos sea ingresado
    if (!dia && !mes) {
        alert('Debe seleccionar al menos un día o mes.');
        return;
    }

    // Realizar la solicitud AJAX para obtener los datos
    $.ajax({
        url: 'reporte_mas_ventas.php',
        type: 'POST',
        data: {
            dia: dia,
            mes: mes
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                let tableRows = '';
                response.referencias.forEach(item => {
                    tableRows += `
                        <tr>
                            <td>${item.ref}</td>
                            <td>${item.color}</td>
                            <td>${item.cantidad}</td>
                        </tr>
                    `;
                });

                $('#tabla-referencias tbody').html(tableRows);
                $('#tabla-referencias').show();
            } else {
                alert('No se encontraron datos para la fecha seleccionada.');
                $('#tabla-referencias').hide();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr.responseText);
            alert('Error al generar el reporte.');
        }
    });
});

        });
    </script>
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
