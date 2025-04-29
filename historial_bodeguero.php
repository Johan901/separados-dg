<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - DG</title>
    <link rel="stylesheet" href="css/styles_historial.css?v=7.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
            <div class="dropdown-menu">
                <a href="bodeguero_panel.php">Inicio</a>
                <a href="agregar_inventario_bodeguero.php">Agregar Inventario</a>
                <a href="inventario_bodeguero.php">Inventario de productos</a>
                <a href="historial_bodeguero.php">Historial de pedidos</a>
            </div>
        </div>
        <a href="bodeguero_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <!-- Sección de filtros -->
<div class="filtro-contenedor">
    <div class="filtro-formulario">
        <form id="filter-form" action="historial_bodeguero.php" method="GET">
            <h2>Filtros</h2>
            <div class="filters-container">
                <label><input type="checkbox" name="estado[]" value="abierto"> Pedidos Abiertos</label>
                <label><input type="checkbox" name="estado[]" value="cerrado"> Pedidos Cerrados</label>
                <label><input type="checkbox" name="cuenta_regresiva_hoy" value="hoy"> Cuenta regresiva termina hoy</label>
                <label><input type="checkbox" name="sin_separar" value="sin_separar"> Pedidos sin separar</label>
            </div>
            <button type="submit" class="button">Aplicar</button>
            <button type="reset" class="button" onclick="window.location.href='historial_bodeguero.php';">Limpiar</button>
        </form>
    </div>

    <!-- Sección de búsquedas -->
    <div class="search-container">
        <!-- Buscar por número de ID pedido -->
        <form action="historial_bodeguero.php" method="GET" class="search-form">
            <label for="id_pedido">Buscar por número de ID pedido:</label>
            <input type="text" name="id_pedido" required>
            <input class="button" type="submit" value="Buscar pedido">
        </form>
    <!-- Buscar por Cédula o nombre cliente -->
        <form action="historial_bodeguero.php" method="GET" class="search-form">
                <label for="buscar">Buscar cliente por nombre o cédula:</label>
                <input type="text" name="buscar" required>
                <input type="submit" value="Buscar">
        </form>
    </div>
</div>
    <script src="js/main_user.js?v=1.1"></script>

    <h2>Historial de Pedidos</h2>

    <?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$id_pedido = null;
$cliente_cedula = null;
$message = '';

// Consulta básica
$query = "SELECT p.id_pedido, p.cliente_cedula, p.fecha_pedido, p.fecha_limite,
                 SUM(dp.subtotal) AS total_pedido, p.asesor, p.envio,
                 c.nombre AS nombre_cliente, p.estado
          FROM pedidos p 
          LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido 
          LEFT JOIN clientes c ON p.cliente_cedula = c.cedula";
$conditions = [];

// Filtros de estado
if (isset($_GET['estado']) && is_array($_GET['estado'])) {
    $estado_filtro = array_map(function($estado) use ($conn) {
        return $conn->quote($estado);
    }, $_GET['estado']);
    $conditions[] = "p.estado IN (" . implode(",", $estado_filtro) . ")";
}

// Filtro para cuenta regresiva hoy
if (isset($_GET['cuenta_regresiva_hoy']) && $_GET['cuenta_regresiva_hoy'] === 'hoy') {
    $conditions[] = "p.fecha_limite::date = CURRENT_DATE";
} 

// Filtro de pedidos sin separar
if (isset($_GET['sin_separar']) && $_GET['sin_separar'] === 'sin_separar') {
    $conditions[] = "EXISTS (SELECT 1 FROM detalle_pedido dp WHERE dp.id_pedido = p.id_pedido AND CAST(dp.actualizado AS INTEGER) = 0)";
}

// Filtro por ID de pedido
if (isset($_GET['id_pedido']) && is_numeric($_GET['id_pedido'])) {
    $id_pedido = $_GET['id_pedido'];
    $conditions[] = "p.id_pedido = :id_pedido";
}

// Filtro por cédula o nombre del cliente, sin importar mayúsculas o minúsculas
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $buscar = '%' . $_GET['buscar'] . '%';  // Usar LIKE para buscar coincidencias parciales
    $conditions[] = "(LOWER(p.cliente_cedula) LIKE LOWER(:buscar) OR LOWER(c.nombre) LIKE LOWER(:buscar))";
}

// Agregar las condiciones de filtro
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Agrupar y ordenar los resultados
$query .= " GROUP BY p.id_pedido, c.nombre, p.estado ORDER BY p.fecha_pedido DESC LIMIT 50";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query);

if (isset($id_pedido)) {
    $stmt->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
}
// Vincular los parámetros si es necesario
if (isset($buscar)) {
    $stmt->bindValue(':buscar', $buscar, PDO::PARAM_STR);
}

try {
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si el resultado está vacío
    if (empty($result)) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin resultados',
                    text: 'No se encontraron pedidos para el filtro seleccionado.',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'historial_bodeguero.php';
                    }
                });
              </script>";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

// Mostrar los resultados en una tabla
if (isset($result) && count($result) > 0) {
    echo "
    <div class='table-container'>
    <table>
        <tr>
            <th>ID Pedido</th>
            <th>Cédula Cliente</th>
            <th>Nombre Cliente</th> 
            <th>Fecha Pedido</th>
            <th>Fecha Límite</th>
            <th>Total Pedido (COP)</th>
            <th>Linea</th>
            <th>Envío</th>
            <th>Estado</th>
            <th>Cuenta Regresiva</th>
            <th>Acciones</th>
            <th>Actualizaciones</th>
        </tr>";

    foreach ($result as $row) {
        // Formatear el total del pedido
        $total_formateado = "$" . number_format($row['total_pedido'], 0, ',', '.');

        // Alerta para el estado de pedidos
        $cliente_cedula = $row['cliente_cedula']; // Obtén la cédula del cliente del $row

        // Mostrar las observaciones
        $observacion = isset($row['observacion']) ? $row['observacion'] : "No hay observaciones";
        
        // Botón para agregar observación
        $agregar_observacion_button = "<a href='agregar_observacion.php?id_pedido=" . $row['id_pedido'] . "'><button class='button'>Agregar Observación</button></a>";

        // Consulta para contar los pedidos del cliente
        $query_count = "SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE cliente_cedula = :cliente_cedula";
        $stmt = $conn->prepare($query_count);
        $stmt->bindParam(':cliente_cedula', $cliente_cedula);
        $stmt->execute();
        $result_count = $stmt->fetch(PDO::FETCH_ASSOC);

        // Alerta según el número de pedidos
        $alerta_nuevo_cliente = '';
        $estado = $row['estado'];  // Asegúrate de que esto obtenga el estado correcto
        $total_pedidos = (int)$result_count['total_pedidos']; // Asegúrate de convertirlo a entero

        if ($total_pedidos == 0) {
            $alerta_nuevo_cliente = "<div style='color: green; font-weight: bold;'>No hay pedidos registrados para este cliente.</div>";
        } elseif ($total_pedidos == 1) {
            if ($estado == 'cerrado') {
                $alerta_nuevo_cliente = "<div style='color: red; font-weight: bold;'>Este es el primer pedido de este cliente, pero el estado es CERRADO. Crea una nueva bolsa si es necesario.</div>";
            } elseif ($estado == 'eliminado') {
                $alerta_nuevo_cliente = "<div style='color: red; font-weight: bold;'>Este pedido ha sido ELIMINADO. Favor desarmar los productos.</div>";
            } else {
                $alerta_nuevo_cliente = "<div style='color: blue; font-weight: bold;'>Este es el primer pedido de este cliente, está ABIERTO. Crea una nueva bolsa si es necesario.</div>";
            }
        } else {
            if ($estado == 'cerrado') {
                $alerta_nuevo_cliente = "<div style='color: red; font-weight: bold;'>Este pedido está CERRADO, favor crear uno nuevo si es necesario.</div>";
            } elseif ($estado == 'eliminado') {
                $alerta_nuevo_cliente = "<div style='color: red; font-weight: bold;'>Este pedido ha sido ELIMINADO. Favor desarmar los productos.</div>";
            } else {
                $alerta_nuevo_cliente = "<div style='color: green; font-weight: bold;'>Este pedido está ABIERTO. Ya hay una bolsa asignada para este cliente.</div>";
            }
        }

        // Consulta para verificar si todos los artículos están separados
        // Consulta para verificar si todos los artículos están separados
$query_separado = "SELECT COUNT(*) AS total, 
                        SUM(CASE WHEN CAST(actualizado AS INTEGER) = 0 THEN 1 ELSE 0 END) AS total_no_actualizado
                        FROM detalle_pedido 
                        WHERE id_pedido = :id_pedido";

$stmt_separado = $conn->prepare($query_separado);
$stmt_separado->bindValue(':id_pedido', $row['id_pedido'], PDO::PARAM_INT);
$stmt_separado->execute();
$result_separado = $stmt_separado->fetch(PDO::FETCH_ASSOC);

// Verificar si hay artículos pendientes por separar
$pendientes = $result_separado['total_no_actualizado'];  // Usar la clave correcta
$mensaje_separado = $pendientes ? "Faltan artículos por separar." : "Todo ha sido separado.";


        // Calcular la cuenta regresiva
        $fecha_limite = new DateTime($row['fecha_limite'], new DateTimeZone('America/Bogota'));
        $fecha_actual = new DateTime("now", new DateTimeZone('America/Bogota'));
        $tiempo_restante = $fecha_actual->diff($fecha_limite);

        // Comprobar si ha expirado
        $ha_expirado = $tiempo_restante->invert === 1;
        $cuenta_regresiva = $ha_expirado ? "<span style='color: red;'><strong>El tiempo de este separado ha sido EXPIRADO.</strong></span>" : $tiempo_restante->format('%d días %h horas %i minutos %s segundos');

        // Establecer el estado color
        $estado_color = $ha_expirado ? "style='color: red;'" : "";
        echo "<tr data-id-pedido='{$row['id_pedido']}'>
                <td>{$row['id_pedido']}</td>
                <td>{$row['cliente_cedula']}</td>
                <td>{$row['nombre_cliente']}</td>
                <td>{$row['fecha_pedido']}</td>
                <td>{$row['fecha_limite']}</td>
                <td>{$total_formateado}</td>
                <td>{$row['asesor']}</td>
                <td>{$row['envio']}</td>
                <td>{$row['estado']} $alerta_nuevo_cliente</td>
                <td $estado_color>$cuenta_regresiva</td>
                <td class='action-buttons'>
                    <a href='detalle_pedido_bodeguero.php?id={$row['id_pedido']}' class='button'>Ver Detalles</a>
                    <a href='editar_pedido_bodeguero.php?id_pedido={$row['id_pedido']}' class='button'>Editar Separado</a>
                    <div class='checkbox-container'>
                    <input type='checkbox'
                            class='checkbox-separado'
                            data-id='<?= $pedido['id'] ?>'
                            <?= $pedido['separado_check'] ? 'checked' : '' ?>>
                    </div>

                <td>' . $mensaje_separado . '</td>
            </tr>";
    }
    echo "</table>
    </div>";
} else {
    $message = "No se encontraron resultados.";
}

echo "
<div id='form-observacion' style='display:none;'>
    <form id='observacion-form'>
        <label for='observacion'>Nueva Observación:</label>
        <input type='text' id='observacion' name='observacion' required>
        <input type='hidden' id='pedido-id' name='pedido-id'>
        <button type='submit'>Agregar</button>
    </form>
</div>";

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

// Función para mostrar el formulario de observación y asignar el ID del pedido
function agregarObservacion(id_pedido) {
    // Mostrar el formulario
    document.getElementById('form-observacion').style.display = 'block';
    document.getElementById('pedido-id').value = id_pedido;
}

// Manejar la sumisión del formulario
document.getElementById('observacion-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const observacion = document.getElementById('observacion').value;
    const id_pedido = document.getElementById('pedido-id').value;

    // Validación simple
    if (!observacion) {
        alert("Por favor, ingrese una observación.");
        return;
    }

    // Realizar una solicitud AJAX para agregar la observación en la base de datos
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'agregar_observacion.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Verificar respuesta del servidor
            if (xhr.responseText == "success") {
                alert("Observación agregada exitosamente");
                location.reload();  // Recargar la página para ver la nueva observación
            } else {
                alert("Error al agregar la observación.");
            }
        }
    };
    xhr.send('observacion=' + encodeURIComponent(observacion) + '&id_pedido=' + encodeURIComponent(id_pedido));
});

$(document).ready(function() {
            // Llama a actualizarEstados cada segundo
            setInterval(actualizarEstados, 1000); // 1000 ms = 1 segundo
});

function actualizarEstados() {
            fetch('obtener_estados.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.error) {
                        data.forEach(pedido => {
                            // Encontrar la fila correspondiente al pedido
                            const fila = document.querySelector(`tr[data-id-pedido="${pedido.id_pedido}"]`);
                            if (fila) {
                                const estadoCelda = fila.querySelector('.estado'); // Asegúrate de tener una clase para la celda del estado
                                estadoCelda.textContent = pedido.estado; // Actualiza el texto del estado
                            }
                        });
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => console.error('Error al obtener los estados:', error));
        }   

// Función para cargar el estado del checklist al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    // Cargar el estado de todos los checkboxes al cargar la página
    loadChecklistStates();

    // Escuchar el cambio en cada checkbox y guardar su estado
    const checkboxes = document.querySelectorAll(".pedido-checkbox");
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", () => {
            toggleChecklist(checkbox);
        });
    });
});

// Función para cargar el estado de todos los checkboxes
function loadChecklistStates() {
    const checkboxes = document.querySelectorAll(".pedido-checkbox");
    checkboxes.forEach(checkbox => {
        const isChecked = localStorage.getItem(checkbox.id) === "true";
        checkbox.checked = isChecked;
    });
}

// Función para guardar el estado de un checkbox específico
function toggleChecklist(checkbox) {
    localStorage.setItem(checkbox.id, checkbox.checked);
}


function actualizarCuentaRegresiva() {
    const celdasCuentaRegresiva = document.querySelectorAll('tr td:nth-child(10)');

    celdasCuentaRegresiva.forEach((celda) => {
        const tiempoTexto = celda.innerText;

        // Solo procesar si el texto no indica que ha expirado
        if (tiempoTexto !== "El tiempo de este separado ha sido EXPIRADO.") {
            const tiempoArray = tiempoTexto.split(' ');

            let dias = parseInt(tiempoArray[0]) || 0;
            let horas = parseInt(tiempoArray[2]) || 0;
            let minutos = parseInt(tiempoArray[4]) || 0;
            let segundos = parseInt(tiempoArray[6]) || 0;

            // Disminuir los segundos
            segundos--;

            // Manejo de los desbordamientos
            if (segundos < 0) {
                segundos = 59;
                minutos--;
            }
            if (minutos < 0) {
                minutos = 59;
                horas--;
            }
            if (horas < 0) {
                horas = 23;
                dias--;
            }

            // Actualizar la celda de cuenta regresiva
            celda.innerText = `${dias} días ${horas} horas ${minutos} minutos ${segundos} segundos`;

            if (dias === 0 && horas < 1) {
                celda.style.color = "red"; // Cambiar a rojo
            } else {
                celda.style.color = ""; // Restablecer color si es mayor a una hora
            }

            // Comprobar si ha expirado
            if (dias <= 0 && horas <= 0 && minutos <= 0 && segundos <= 0) {
                celda.innerText = "El tiempo de este separado ha sido EXPIRADO.";
                celda.style.color = "red"; 

                // Obtener la cédula del cliente desde la fila correspondiente
                const fila = celda.parentElement;
                const clienteCedula = fila.children[1].innerText; // Asumiendo que la cédula está en la segunda columna
                const pedidoId = fila.children[0].innerText; // Asumiendo que el ID del pedido está en la primera columna

                // Realizar la llamada AJAX para actualizar el estado
                fetch('actualizar_estado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id_pedido': pedidoId,
                    }),
                })
                .then(response => response.json())
                .then(data => { 
                    if (data.success) {
                        // Actualizar el estado en la tabla
                        fila.children[8].innerText = 'cerrado'; // Cambiar la columna de estado a 'cerrado'
                    } else {
                        console.error('Error al actualizar el estado');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    });
}

// Llama a la función de actualización de cuenta regresiva cada segundo
setInterval(actualizarCuentaRegresiva, 1000);



// JavaScript para seleccionar/deseleccionar todos los checkboxes
document.getElementById('select_all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.pedido-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

</script>

<style>
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #ffcc00;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        font-family: Arial, sans-serif;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .notification .close {
        cursor: pointer;
        font-weight: bold;
        margin-left: auto;
    }
</style>

<div id="notifications"></div>  <!-- Contenedor de notificaciones -->

<script>
const eventSource = new EventSource('/escuchar_notificacion.php');

eventSource.onmessage = function(event) {
    try {
        const data = JSON.parse(event.data);

        // 🔥 Asegurar que la notificación es nueva y válida
        if (data.id_pedido && data.observaciones) {
            console.log("✅ Nueva notificación recibida:", data);
            showNotification(`Nueva observación en el pedido ${data.id_pedido}: ${data.observaciones}`);
        }
    } catch (error) {
        console.error("Error al procesar la notificación:", error);
    }
};

// Mostrar notificación
function showNotification(message) {
    const notification = document.createElement("div");
    notification.className = "notification";
    notification.innerHTML = `<span>${message}</span> <span class="close">✖</span>`;

    notification.querySelector(".close").addEventListener("click", function() {
        notification.remove();
    });

    document.getElementById("notifications").appendChild(notification);
}

</script>

<script>
document.querySelectorAll('.checkbox-separado').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const pedidoId = this.dataset.id;
        const separado = this.checked ? 1 : 0;

        fetch('actualizar_separado.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${pedidoId}&separado=${separado}`
        })
        .then(response => response.text())
        .then(data => {
            console.log('Servidor dijo:', data);
        })
        .catch(error => {
            console.error('Error al actualizar:', error);
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

</body>
</html>
