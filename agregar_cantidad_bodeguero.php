<?php
include 'config.php'; // Incluir la conexión

$response = ""; // Variable para manejar la respuesta

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Si se envía el formulario, procesa e inserta en la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $ref = $_POST['ref']; // Referencia
    $color = $_POST['color']; // Color
    $cantidad = $_POST['cantidad']; // Cantidad
    
    try {
        // Actualizar la cantidad en la base de datos
        $query = "UPDATE inventario SET cantidad = :cantidad WHERE ref = :ref AND color = :color";
        
        $stmt = $conn->prepare($query);
        
        // Vincular los parámetros
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':cantidad', $cantidad);
        
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
    <title>Actualizar Cantidad - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_editar_user.css?v=4.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
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

    <h2>Actualizar Cantidad en Inventario</h2>

    <!-- Formulario para actualizar cantidad -->
    <form action="agregar_cantidad_bodeguero.php" method="post" class="user-edit-form">
        <label for="ref">Referencia:</label>
        <input type="text" name="ref" value="<?= isset($_GET['ref']) ? $_GET['ref'] : '' ?>" readonly>

        <label for="color">Color:</label>
        <input type="text" name="color" value="<?= isset($_GET['color']) ? $_GET['color'] : '' ?>" readonly>

        <label for="cantidad">Cantidad Actual:</label>
        <input type="number" name="cantidad" value="<?= isset($_GET['cantidad']) ? $_GET['cantidad'] : '' ?>" required min="0">

        <input type="submit" value="Actualizar Cantidad">
    </form>

    <!-- Footer -->
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
            &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de Separodos e Inventario.
        </div>
    </footer>

    <!-- Script para manejar la respuesta -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
        // Manejar la respuesta después de que se haya enviado el formulario
        window.onload = function() {
            <?php if ($response == "success") : ?>
                swal("Éxito!", "Cantidad actualizada con éxito.", "success").then(() => {
                    window.location.href = 'bodeguero_panel.php';
                });
            <?php elseif (strpos($response, "error") !== false) : ?>
                swal("Error!", "<?= $response ?>", "error");
            <?php endif; ?>
        }
    </script>

    <script src="js/main_user.js?v=1.1"></script>
</body>
</html>
