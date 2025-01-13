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
    $tipo_prenda = $_POST['tipo_prenda']; // Tipo de prenda
    $color = $_POST['color']; // Color
    $cantidad = $_POST['cantidad']; // Cantidad
    $precio_al_detal = $_POST['precio_al_detal']; // Precio al detalle
    $precio_por_mayor = $_POST['precio_por_mayor']; // Precio por mayor
    
    try {
        // Verificar si la referencia ya existe
        $checkQuery = "SELECT COUNT(*) FROM inventario WHERE ref = :ref AND color = :color";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':ref', $ref);
        $checkStmt->bindParam(':color', $color);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();


        if ($count > 0) {
            // La referencia ya existe, devolver error
            $response = "duplicate";
        } else {
            // Insertar nueva referencia si no existe
            $query = "INSERT INTO inventario (ref, tipo_prenda, color, cantidad, precio_al_detal, precio_por_mayor) 
                      VALUES (:ref, :tipo_prenda, :color, :cantidad, :precio_al_detal, :precio_por_mayor)";
            
            $stmt = $conn->prepare($query);

            // Vincular los parámetros
            $stmt->bindParam(':ref', $ref);
            $stmt->bindParam(':tipo_prenda', $tipo_prenda);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':precio_al_detal', $precio_al_detal);
            $stmt->bindParam(':precio_por_mayor', $precio_por_mayor);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $response = "success";
            } else {
                $response = "error";
            }
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
    <title>Agregar Nueva Referencia - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_editar_user.css?v=4.1">
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
                <a href="bodeguero_panel.php">Inicio</a>
                <a href="agregar_inventario_bodeguero.php">Agregar Inventario</a>
                <a href="inventario_bodeguero.php">Inventario de productos</a>
                <a href="historial_bodeguero.php">Historial de pedidos</a>
            </div>
        </div>
        <a href="bodeguero_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <h2>Agregar Nueva Referencia</h2>

    <!-- Formulario para agregar referencia -->
    <form action="agregar_inventario_bodeguero.php" method="post" class="user-edit-form">
        <label for="ref">Referencia:</label>
        <input type="text" name="ref" required>

        <label for="tipo_prenda">Tipo de Prenda:</label>
        <input type="text" name="tipo_prenda" required>

        <label for="color">Color:</label>
        <input type="text" name="color" required>

        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" required min="0">

        <label for="precio_al_detal">Precio al Detal: (SOLO EL NUMERO) </label>
        <input type="number" name="precio_al_detal" required step="0.01">

        <label for="precio_por_mayor">Precio por Mayor: (SOLO EL NUMERO)</label>
        <input type="number" name="precio_por_mayor" required step="0.01">

        <input type="submit" value="Agregar">
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
                swal("Éxito!", "Referencia agregada con éxito.", "success").then(() => {
                    window.location.href = 'admin_panel.php';
                });
            <?php elseif ($response == "duplicate") : ?>
                swal("Error!", "La referencia ya está registrada.", "error");
            <?php elseif (strpos($response, "error") !== false) : ?>
                swal("Error!", "<?= $response ?>", "error").then(() => {
                    window.location.href = 'admin_panel.php';
                });
            <?php endif; ?>
        }
    </script>

    <script src="js/main_user.js?v=1.1"></script>
</body>
</html>
