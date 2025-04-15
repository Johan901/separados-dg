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
    $mercancia_nueva = $_POST['mercancia_nueva']; // Si es mercancía nueva

    try {
        // Verificar si la referencia ya existe en inventario
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
            // Si es mercancía nueva, insertar en la tabla 'mercancia_nueva'
            if ($mercancia_nueva == 'si') {
                $query_nueva = "INSERT INTO mercancia_nueva (ref, tipo_prenda, color, cantidad, precio_al_detal, precio_por_mayor, fecha_creacion) 
                                VALUES (:ref, :tipo_prenda, :color, :cantidad, :precio_al_detal, :precio_por_mayor, NOW())";

                $stmt_nueva = $conn->prepare($query_nueva);
                // Vincular los parámetros
                $stmt_nueva->bindParam(':ref', $ref);
                $stmt_nueva->bindParam(':tipo_prenda', $tipo_prenda);
                $stmt_nueva->bindParam(':color', $color);
                $stmt_nueva->bindParam(':cantidad', $cantidad);
                $stmt_nueva->bindParam(':precio_al_detal', $precio_al_detal);
                $stmt_nueva->bindParam(':precio_por_mayor', $precio_por_mayor);

                // Ejecutar la consulta para 'mercancia_nueva'
                if ($stmt_nueva->execute()) {
                    $response = "success_new";
                } else {
                    $response = "error_new";
                }
            } else {
                // Si es mercancía vieja, insertar solo en inventario
                $query_inventario = "INSERT INTO inventario (ref, tipo_prenda, color, cantidad, precio_al_detal, precio_por_mayor) 
                                     VALUES (:ref, :tipo_prenda, :color, :cantidad, :precio_al_detal, :precio_por_mayor)";

                $stmt_inventario = $conn->prepare($query_inventario);
                // Vincular los parámetros
                $stmt_inventario->bindParam(':ref', $ref);
                $stmt_inventario->bindParam(':tipo_prenda', $tipo_prenda);
                $stmt_inventario->bindParam(':color', $color);
                $stmt_inventario->bindParam(':cantidad', $cantidad);
                $stmt_inventario->bindParam(':precio_al_detal', $precio_al_detal);
                $stmt_inventario->bindParam(':precio_por_mayor', $precio_por_mayor);

                // Ejecutar la consulta para 'inventario'
                if ($stmt_inventario->execute()) {
                    $response = "success_old";
                } else {
                    $response = "error_old";
                }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
</head>
<body>
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

        <!-- Pregunta sobre mercancía nueva -->
        <label>¿Es mercancía nueva?</label><br>
        <input type="radio" name="mercancia_nueva" value="si" required> Sí, es nueva
        <input type="radio" name="mercancia_nueva" value="no" required> No, es vieja

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
    <script>
    window.onload = function() {
        <?php if ($response == "success_new") : ?>
            Swal.fire("Éxito!", "Mercancía nueva agregada con éxito.", "success").then(() => {
                window.location.href = 'bodeguero_panel.php'; // Redirige a bodeguero_panel.php
            });
        <?php elseif ($response == "success_old") : ?>
            Swal.fire("Éxito!", "Mercancía agregada con éxito.", "success").then(() => {
                window.location.href = 'bodeguero_panel.php'; // Redirige a bodeguero_panel.php
            });
        <?php elseif ($response == "duplicate") : ?>
            Swal.fire("Error!", "La referencia ya está registrada.", "error");
        <?php elseif (strpos($response, "error") !== false) : ?>
            Swal.fire("Error!", "<?= $response ?>", "error").then(() => {
                window.location.href = 'bodeguero_panel.php'; // Redirige a bodeguero_panel.php si hay un error
            });
        <?php endif; ?>
    }
    </script>


    <script src="js/main_user.js?v=1.1"></script>
</body>
</html>
