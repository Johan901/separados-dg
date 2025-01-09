<?php
include 'config.php'; // Incluye la configuración de la conexión a la base de datos

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Verifica si se ha pasado un ID de pedido en la URL
if (isset($_GET['id_pedido'])) {
    $id_pedido = $_GET['id_pedido'];
} else {
    header("Location: admin_panel.php?msg=ID de pedido no proporcionado");
    exit();
}

// Variable para mensajes
$msg = "";

// Si se envía el formulario, procesa y actualiza la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $envio = $_POST['envio'];
    $fecha_pedido = $_POST['fecha_pedido'];
    $fecha_limite = $_POST['fecha_limite'];
    $observaciones = $_POST['observaciones']; // Nueva observación

    if (empty(trim($observaciones))) {
        $msg = "Debe ingresar una nueva observación para actualizar el pedido.";
    } else {
        // Actualiza el pedido si la observación no está vacía
        $query = "UPDATE pedidos SET envio=?, fecha_pedido=?, fecha_limite=?, observaciones=? WHERE id_pedido=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$envio, $fecha_pedido, $fecha_limite, $observaciones, $id_pedido]);

        if ($stmt->rowCount() > 0) {
            $msg = "Pedido actualizado con éxito.";
        } else {
            $msg = "No se realizaron cambios o hubo un error.";
        }
    }
}

// Obtener los datos del pedido para rellenar el formulario
$query = "SELECT * FROM pedidos WHERE id_pedido=?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_pedido]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el pedido fue encontrado
if (!$pedido) {
    header("Location: admin_panel.php?msg=Pedido no encontrado");
    exit();
}

// Definir observaciones y botón de agregar observación
$observaciones = isset($pedido['observaciones']) && trim($pedido['observaciones']) !== "No hay observaciones" 
    ? $pedido['observaciones'] 
    : "";

// Asegúrate de que las fechas están en el formato correcto (incluyendo hora)
$fecha_pedido = isset($pedido['fecha_pedido']) ? date('Y-m-d\TH:i', strtotime($pedido['fecha_pedido'])) : '';
$fecha_limite = isset($pedido['fecha_limite']) ? date('Y-m-d\TH:i', strtotime($pedido['fecha_limite'])) : '';

// Obtener los detalles del pedido
$query_detalle = "SELECT * FROM detalle_pedido WHERE id_pedido=?";
$stmt_detalle = $conn->prepare($query_detalle);
$stmt_detalle->execute([$id_pedido]);
$detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_pedidos.css?v=4.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="js/main_user.js?v=2.0"></script>

</head>
<body>
    <!-- Header -->
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

    <h2>Editar Pedido</h2>

    <form action="editar_pedido.php?id_pedido=<?php echo $id_pedido; ?>" method="post" class="pedido-edit-form">
        <label for="envio" class="form-label">Envío:</label>
        <input type="text" name="envio" value="<?php echo htmlspecialchars($pedido['envio']); ?>" required class="form-input button">

        <label for="fecha_pedido" class="form-label">Fecha de Pedido:</label>
        <input type="datetime-local" name="fecha_pedido" value="<?php echo $fecha_pedido; ?>" required class="form-input button">

        <label for="fecha_limite" class="form-label">Fecha Límite:</label>
        <input type="datetime-local" name="fecha_limite" value="<?php echo $fecha_limite; ?>" required class="form-input button">

         <!-- Observación general -->
        <label for="observacion" class="form-label">Ingrese una observación:</label>
        <textarea name="observaciones" class="form-input button" rows="4" placeholder="Ingrese una observación" required><?php echo htmlspecialchars($observaciones); ?></textarea>

        <!-- Botón centrado al final del formulario -->
        <div style="text-align: center; margin-top: 20px;">
            <input type="submit" value="Actualizar Pedido" class="button">
        </div>
    </form>

    <h3>Detalles del Pedido</h3>
    <table>
        <thead>
            <tr>
                <th>Referencia</th>
                <th>Color</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $detalle): ?>
                <tr>
                    <td><?php echo htmlspecialchars($detalle['ref']); ?></td>
                    <td><?php echo htmlspecialchars($detalle['color']); ?></td>
                    <td id="cantidad_<?php echo $detalle['id_detalle']; ?>"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                    <td><?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                    <td id="subtotal_<?php echo $detalle['id_detalle']; ?>" data-precio-unitario="<?php echo htmlspecialchars($detalle['precio_unitario']); ?>">
                        <?php echo number_format($detalle['subtotal'], 2); ?>                    <td>
                        <button type="button" class="button" onclick="eliminarProducto(<?php echo $detalle['id_detalle']; ?>)">Eliminar</button>
                        <button type="button" class="button" onclick="modificarCantidad(<?php echo $detalle['id_detalle']; ?>, -1)">-</button>
                        <button type="button" class="button" onclick="modificarCantidad(<?php echo $detalle['id_detalle']; ?>, 1)">+</button>
                        <!-- Checkbox de pago -->
                        <div class='checkbox-container'>
                            <input type='checkbox' class='pedido-checkbox' id='pedido_<?php echo $detalle['id_detalle']; ?>'>
                            <label for='pedido_<?php echo $detalle['id_detalle']; ?>'>¿PAGO?</label>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="button" class="custom-button" onclick="eliminarTodoPedido()">Eliminar Todo el Pedido</button>
            
<?php if ($msg): ?>
<script>
            Swal.fire({
                icon: '<?php echo $stmt ? "success" : "error"; ?>',
                title: '<?php echo $stmt ? "Éxito" : "Error"; ?>',
                text: '<?php echo $msg; ?>',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "admin_panel.php"; // Redirigir a admin_panel.php después de aceptar
                }
            });
        </script>
    <?php endif; ?>

    <script>

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
        
        function eliminarProducto(id_detalle) {
            // Lógica para eliminar el producto del detalle
            Swal.fire({
                title: '¿Estás seguro de que quieres eliminar este producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('eliminar_producto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id_detalle: id_detalle, id_pedido: <?php echo $id_pedido; ?> })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado!', data.message, 'success').then(() => {
                                location.reload(); // Recarga la página para actualizar la lista
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    });
                }
            });
        }

    function modificarCantidad(id_detalle, cambio) {
    const cantidadCell = document.getElementById('cantidad_' + id_detalle);
    const subtotalCell = document.getElementById('subtotal_' + id_detalle);
    let cantidadActual = parseInt(cantidadCell.textContent);

    // Asegúrate de que la cantidad no sea negativa
    if (cantidadActual + cambio < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se puede tener una cantidad negativa.'
        });
        return;
    }

    // Actualiza la cantidad y el subtotal
    cantidadActual += cambio;
    cantidadCell.textContent = cantidadActual;
    const precioUnitario = parseFloat(subtotalCell.getAttribute('data-precio-unitario'));
    const nuevoSubtotal = cantidadActual * precioUnitario;
    subtotalCell.textContent = new Intl.NumberFormat("es-CO").format(nuevoSubtotal);

    // Enviar el cambio al servidor
    fetch('actualizar_cantidad.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_detalle: id_detalle, nueva_cantidad: cantidadActual })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            Swal.fire('Error!', data.message, 'error');
        } else {
            actualizarTotalPedido(); // Llama a la función para actualizar el total
        }
    });
}


function actualizarTotalPedido() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal_"]').forEach(subtotalCell => {
        const subtotalValue = parseFloat(subtotalCell.textContent.replace(/\./g, '').replace(',', '.'));
        console.log(`Subtotal: ${subtotalValue}`); // Debugging
        total += subtotalValue;
    });

    console.log(`Total antes de formatear: ${total}`); // Debugging
    document.getElementById('total_pedido').textContent = new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        minimumFractionDigits: 0
    }).format(total);
}


        function eliminarTodoPedido() {
            Swal.fire({
                title: '¿Estás seguro de que quieres eliminar todo el pedido?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('eliminar_todo_pedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id_pedido: <?php echo $id_pedido; ?> })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado!', data.message, 'success').then(() => {
                                window.location.href = "admin_panel.php"; // Redirigir a admin_panel.php después de eliminar
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    });
                }
            });
        }
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
