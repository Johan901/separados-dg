<?php
include 'config.php'; // Incluir la conexión

$response = ""; // Variable para manejar la respuesta

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Si se envía el formulario, procesa e inserta la observación en la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $observacion = $_POST['observacion']; // Observación
    $id_pedido = $_POST['id_pedido']; // ID del pedido
    
    try {
        // Actualizar la observación en la tabla pedidos
        $query = "UPDATE pedidos SET observaciones = :observacion WHERE id_pedido = :id_pedido";
        $stmt = $conn->prepare($query);
        
        // Vincular los parámetros
        $stmt->bindParam(':observacion', $observacion);
        $stmt->bindParam(':id_pedido', $id_pedido);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            $response = "success";
        } else {
            $response = "error";
        }
    } catch (PDOException $e) {
        $response = "error: " . addslashes($e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Observación</title>
    <link rel="stylesheet" href="css/styles_editar_user.css?v=5.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
            <div class="dropdown-menu">
                <a href="admin_panel.php">Inicio</a>
                <a href="agregar_observacion.php">Agregar Observación</a>
                <a href="info_cliente.php">Ver clientes</a>
                <a href="inventario.php">Inventario de productos</a>
                <a href="nuevo_pedido.php">Agregar nuevo pedido</a>
                <a href="historial_pedidos.php">Historial de pedidos</a>
            </div>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <h2>Agregar Observación</h2>

    <form action="agregar_observacion.php" method="post" class="user-edit-form">
        <label for="observacion">Observación:</label>
        <textarea name="observacion" rows="4" required></textarea>

        <input type="submit" value="Agregar">
    </form>

    <!-- Footer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
    // Manejar la respuesta después de que se haya enviado el formulario
    window.onload = function() {
        <?php if ($response == "success") : ?>
            swal("Éxito!", "Observación agregada con éxito.", "success").then(() => {
                window.location.href = 'asesor_panel.php';
            });
        <?php elseif (strpos($response, "error") !== false) : ?>
            swal("Error!", "<?= $response ?>", "error").then(() => {
                window.location.href = 'asesor_panel.php';
            });
        <?php endif; ?>
    }
</script>

    <script src="js/main_user.js?v=1.1"></script>
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
        &copy; 2024 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de Separados e Inventario.
    </div>
</footer>

</html>
