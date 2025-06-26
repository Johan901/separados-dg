<?php
// Incluye la conexión a la base de datos
include('config.php');

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
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
    <title>Panel de Administración - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_admin_panel.css?v=1.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
            <div class="dropdown-menu">
            <a href="asesor_panel.php">Inicio</a>
            <a href="agregar_usuario_asesor.php">Agregar nuevo cliente</a>
            <a href="info_cliente_asesor.php">Info de Clientes</a>
            <a href="nuevo_pedido_asesor.php">Agregar nuevo pedido</a>
            <a href="historial_asesor.php">Historial de pedidos</a>
            </div>
        </div>
        <a href="asesor_panel.php" class="logo">Dulce Guadalupe</a>
        <div class="header-right">
            <form action="info_cliente.php" method="GET" class="search-form">
                <h2><label for="buscar">Buscar cliente por nombre o cédula:</label></h2>
                <input type="text" name="buscar" required>
                <input type="submit" value="Buscar">
            </form>
            
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="titu1">
    <h2>Gestión de Clientes</h2>
    </div>
   

    <?php
    // Consulta de usuarios
    $query = "SELECT * FROM clientes";
    
    // Verificar si se ha enviado un nombre o cédula para buscar
    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = $_GET['buscar'];

        // Query para buscar clientes por nombre o cédula
        $query .= " WHERE LOWER(REPLACE(nombre, ' ', '')) LIKE LOWER(REPLACE(:buscar, ' ', '')) OR cedula = :buscar";
        $stmt = $conn->prepare($query);
        
        // Separamos el valor de búsqueda para nombre y cédula
        $stmt->bindValue(':buscar', '%' . str_replace(' ', '', $buscar) . '%'); // Para nombre
        // Verificamos si el valor es un número (cédula)
        if (is_numeric($buscar)) {
            $stmt->bindValue(':buscar', $buscar); // Para cédula
        } else {
            $stmt->bindValue(':buscar', '%' . str_replace(' ', '', $buscar) . '%'); // Para nombre
        }
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo '<div class="titu1">'; // Cambiado a comillas simples
            echo "<h2>Resultados de la búsqueda para '$buscar'</h2>";
            echo '</div>';
        } else {
            echo '<div class="titu1">'; // Cambiado a comillas simples
            echo "No se encontraron clientes con el nombre o cédula: $buscar";
            echo '</div>';
        }
    } else {
        $stmt = $conn->query($query);
        echo '<div class="titu1">'; // Cambiado a comillas simples
        echo '<h2>Lista de Clientes</h2>';
        echo '</div>';
    }

    // Mostrar los resultados en una tabla
    echo "<table border='1'>
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>País</th>
            <th>Departamento</th>
            <th>Ciudad</th>
            <th>Dirección</th>
            <th>Sexo</th>
            <th>Edad</th>
        </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['cedula']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefono']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['pais']) . "</td>";
        echo "<td>" . htmlspecialchars($row['departamento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ciudad']) . "</td>";
        echo "<td>" . htmlspecialchars($row['direccion']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sexo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['edad']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

    <script>
        function confirmarEliminacion(cedula) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas eliminar este usuario?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "eliminar_usuario.php?cedula=" + encodeURIComponent(cedula);
                }
            });
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
         &copy; 2025 Dulce Guadalupe | Todos los derechos reservados | Sistema de Gestión de separodos e Inventario.
    </div>
</footer>

</html>
