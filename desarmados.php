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
                <a href="reportes.php">Reportes</a>
            </div>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="container">
        <h1>Pedidos Desarmados</h1>
        
        <!-- Botón para mostrar los pedidos desarmados -->
        <button class="button" id="btn-show-deactivated" type="button">Mostrar Pedidos Desarmados</button>

        <!-- Tabla de pedidos desarmados -->
        <table id="tabla-pedidos-desarmados" border="1" style="width: 100%; border-collapse: collapse; display: none;">
            <thead>
                <tr>
                    <th>Asesor</th>
                    <th>Cédula Cliente</th>
                    <th>Envío</th>
                    <th>Estado</th>
                    <th>Fecha Límite</th>
                    <th>Fecha Pedido</th>
                    <th>ID Pedido</th>
                    <th>Medio Conocimiento</th>
                    <th>Observaciones</th>
                    <th>Pedido Separado</th>
                    <th>Total Pedido</th>
                    <th>Ver Referencias</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se llenan dinámicamente con AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
        // Evento para cargar pedidos desarmados al hacer clic en el botón
        $('#btn-show-deactivated').on('click', function () {
            $.ajax({
                url: 'pedidos_back.php',
                type: 'POST',
                data: { action: 'fetch_deactivated' },
                dataType: 'json', 
                success: function (response) {
                    console.log('Pedidos recibidos:', response);

                    if (response.pedidos && response.pedidos.length > 0) {
                        let tableRows = '';
                        response.pedidos.forEach(item => {
                            tableRows += `
                                <tr>
                                    <td>${item.asesor}</td>
                                    <td>${item.cliente_cedula}</td>
                                    <td>${item.envio}</td>
                                    <td>${item.estado}</td>
                                    <td>${item.fecha_limite}</td>
                                    <td>${item.fecha_pedido}</td>
                                    <td>${item.id_pedido}</td>
                                    <td>${item.medio_conocimiento}</td>
                                    <td>${item.observaciones}</td>
                                    <td>${item.pedido_separado ? 'Sí' : 'No'}</td>
                                    <td>${item.total_pedido}</td>
                                    <td id="ref-${item.id_pedido}">Cargando...</td>
                                </tr>
                            `;

                            fetchReferences(item.id_pedido);
                        });

                        $('#tabla-pedidos-desarmados tbody').html(tableRows);
                        $('#tabla-pedidos-desarmados').show();
                    } else {
                        alert('No hay pedidos desarmados.');
                        $('#tabla-pedidos-desarmados').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    alert('Error al cargar los datos.');
                }
            });
        });

        // Función para mostrar referencias (puedes agregar la lógica en main_user.js)
        // Función para obtener referencias y colores de cada pedido
        function fetchReferences(idPedido) {
            $.ajax({
                url: 'detalle_pedido_back.php',
                type: 'POST',
                data: { action: 'fetch_references', id_pedido: idPedido },
                dataType: 'json',
                success: function (response) {
                    let refCell = $(`#ref-${idPedido}`);

                    if (response.length > 0) {
                        let refContent = response.map(item => `Ref: ${item.ref}, Color: ${item.color}`).join('<br>');
                        refCell.html(refContent);
                    } else {
                        refCell.html('No disponible');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    $(`#ref-${idPedido}`).html('Error');
                }
            });
        }

    </script>
</body>
</html>
