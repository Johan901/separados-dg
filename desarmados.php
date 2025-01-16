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
                    <th>Cedula Cliente</th>
                    <th>Envio</th>
                    <th>Estado</th>
                    <th>Fecha Limite</th>
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
    // Mostrar pedidos desarmados (estado "eliminado")
    $('#btn-show-deactivated').on('click', function () {
        $.ajax({
            url: 'pedidos_back.php',
            type: 'POST',
            data: { action: 'fetch_deactivated' },
            success: function (response) {
                try {
                    const data = JSON.parse(response);

                    if (data.pedidos && data.pedidos.length > 0) {
                        let tableRows = '';
                        data.pedidos.forEach(item => {
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
                                    <td>
                                        <select onchange="showReferences(${item.id_pedido})">
                                            <option value="">Seleccionar</option>
                                        </select>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#tabla-pedidos-desarmados tbody').html(tableRows);
                        $('#tabla-pedidos-desarmados').show();
                    } else {
                        alert('No hay pedidos desarmados.');
                        $('#tabla-pedidos-desarmados').hide();
                    }
                } catch (error) {
                    console.error('Error al procesar los datos:', error);
                }
            },
            error: function () {
                alert('Error al cargar los datos.');
            }
        });
    });
});

function showReferences(id_pedido) {
    $.ajax({
        url: 'detalle_pedido_back.php',
        type: 'POST',
        data: { action: 'fetch_references', id_pedido: id_pedido },
        success: function (response) {
            const references = JSON.parse(response);
            const select = $(`select[data-id="${id_pedido}"]`);
            select.empty();
            references.forEach(reference => {
                select.append(new Option(reference.ref, reference.id_detalle));
            });
        },
        error: function () {
            alert('Error al cargar las referencias.');
        }
    });
}
</script>
</body>
</html>
