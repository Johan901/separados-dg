<?php
include 'config.php'; // Incluye la configuración de la conexión a la base de datos

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Verifica si se ha pasado un cedula en la URL
if(isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
} else {
    header("Location: admin_panel.php?msg=ID de cliente no proporcionado");
    exit;
}

// Variable para mensajes
$msg = "";

// Si se envía el formulario, procesa y actualiza la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $pais = $_POST['pais'];
    $departamento = $_POST['departamento'];
    $ciudad = $_POST['ciudad'];
    $sexo = $_POST['sexo'];
    $edad = $_POST['edad'];

    // Actualiza los datos en la base de datos
    $query = "UPDATE clientes SET nombre=?, telefono=?, email=?, pais=?, departamento=?, ciudad=?, direccion=?, sexo=?, edad=? WHERE cedula=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nombre, $telefono, $email, $pais, $departamento, $ciudad, $direccion, $sexo, $edad, $cedula]);

    // Mensaje para mostrar en el alert
    if($stmt) {
        $msg = "Cliente actualizado con éxito";
    } else {
        $msg = "Error al actualizar el cliente";
    }
}

// Obtener los datos del cliente para rellenar el formulario
$query = "SELECT * FROM clientes WHERE cedula=?";
$stmt = $conn->prepare($query);
$stmt->execute([$cedula]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - DG</title>
    <link rel="stylesheet" href="css/styles_editar_user.css?v=1.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

    <h2>Editar Clientes</h2>

    <form action="editar_usuario.php?cedula=<?php echo $cedula; ?>" method="post" class="user-edit-form">
        <label for="nombre" class="form-label">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $cliente['nombre']; ?>" required class="form-input">

        <label for="direccion" class="form-label">Dirección:</label>
        <input type="text" name="direccion" value="<?php echo $cliente['direccion']; ?>" required class="form-input">

        <label for="telefono" class="form-label">Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo $cliente['telefono']; ?>" required class="form-input">

        <label for="email" class="form-label">Correo Electrónico:</label>
        <input type="email" name="email" value="<?php echo $cliente['email']; ?>" required class="form-input">

        <label for="pais" class="form-label">País:</label>
        <input type="text" name="pais" value="<?php echo $cliente['pais']; ?>" required class="form-input">

        <label for="departamento" class="form-label">Departamento:</label>
        <input type="text" name="departamento" value="<?php echo $cliente['departamento']; ?>" required class="form-input">

        <label for="ciudad" class="form-label">Ciudad:</label>
        <input type="text" name="ciudad" value="<?php echo $cliente['ciudad']; ?>" required class="form-input">

        <label for="sexo" class="form-label">Sexo:</label>
        <input type="text" name="sexo" value="<?php echo $cliente['sexo']; ?>" required class="form-input">

        <label for="edad" class="form-label">Edad:</label>
        <input type="number" name="edad" value="<?php echo $cliente['edad']; ?>" required class="form-input">

        <input type="submit" value="Actualizar" class="button">
    </form>

    <?php if ($msg): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            &copy; 2025 Dulce Guadalupe | Todos los derechos reservados
        </div>
    </footer>

    <script src="js/main_user.js?v=1.1"></script>
</body>
</html>
