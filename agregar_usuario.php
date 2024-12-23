<?php
include 'config.php'; // Incluir la conexión

$response = ""; // Variable para manejar la respuesta


// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Si se envía el formulario, procesa e inserta a la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $cedula = $_POST['cedula']; // Cédula
    $nombre_cliente = $_POST['nombre']; // Nombre
    $telefono = $_POST['telefono']; // Teléfono
    $email = $_POST['email']; // Correo Electrónico
    $pais = $_POST['pais']; // País
    $departamento = $_POST['departamento']; // Departamento
    $ciudad = $_POST['ciudad']; // Ciudad
    $direccion = $_POST['direccion']; // Dirección
    $sexo = $_POST['sexo']; // Sexo
    $edad = $_POST['edad']; // Edad
    
    try {
        // Verificar si la cédula ya existe
        $checkQuery = "SELECT COUNT(*) FROM clientes WHERE Cedula = :cedula";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':cedula', $cedula);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // La cédula ya existe, devolver error
            $response = "duplicate";
        } else {
            // Insertar nuevo cliente si no existe la cédula
            $query = "INSERT INTO clientes (Cedula, Nombre, Telefono, Email, Pais, Departamento, Ciudad, Direccion, Sexo, Edad) 
                      VALUES (:cedula, :nombre, :telefono, :email, :pais, :departamento, :ciudad, :direccion, :sexo, :edad)";
            
            $stmt = $conn->prepare($query);

            // Vincular los parámetros
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombre', $nombre_cliente);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':pais', $pais);
            $stmt->bindParam(':departamento', $departamento);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':sexo', $sexo);
            $stmt->bindParam(':edad', $edad);

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
    <title>Panel de Administración - DG</title>
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

    <h2>Agregar Clientes</h2>

    <form action="agregar_usuario.php" method="post" class="user-edit-form">
        <label for="cedula">Cédula:</label>
        <input type="text" name="cedula" required>
        
        <label for="nombre">Nombre Completo:</label>
        <input type="text" name="nombre" required>
        
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" required>
        
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required>
        
        <label for="pais">País:</label>
        <input type="text" name="pais" required>
        
        <label for="departamento">Departamento:</label>
        <input type="text" name="departamento" required>
        
        <label for="ciudad">Ciudad:</label>
        <input type="text" name="ciudad" required>
        
        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" required>
        
        <label for="sexo">Sexo:</label>
        <select name="sexo" required>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
        </select>

        <label for="edad">Edad:</label>
        <input type="number" name="edad" required min="0">

        <input type="submit" value="Agregar">
    </form>

    <!-- Footer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script>
    // Manejar la respuesta después de que se haya enviado el formulario
    window.onload = function() {
        <?php if ($response == "success") : ?>
            swal("Éxito!", "Cliente agregado con éxito.", "success").then(() => {
                window.location.href = 'admin_panel.php';
            });
        <?php elseif ($response == "duplicate") : ?>
            swal("Error!", "El cliente con esta cédula ya está registrado.", "error");
        <?php elseif (strpos($response, "error") !== false) : ?>
            swal("Error!", "<?= $response ?>", "error").then(() => {
                window.location.href = 'admin_panel.php';
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
        &copy; 2024 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de separodos e Inventario.
    </div>
</footer>

</html>
