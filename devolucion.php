<?php
// Verifica si el usuario ha iniciado sesión
session_start();
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
    <title>Devolución de Productos - Dulce Guadalupe</title>
    <link rel="stylesheet" href="css/styles_admin_panel.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <header>
        <div class="hamburger-menu">
            <i class="fas fa-bars"></i>
            <div class="dropdown-menu">
                <a href="admin_panel.php">Inicio</a>
                <a href="inventario.php">Inventario</a>
                <a href="devoluciones.php">Devoluciones</a>
                <!-- Más enlaces -->
            </div>
        </div>
        <a href="admin_panel.php" class="logo">Dulce Guadalupe</a>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <div class="container">
        <h1>Registrar Devolución de Producto</h1>
        
        <label for="referencia">Referencia del Producto:</label>
        <input type="text" id="referencia" name="referencia" placeholder="Ingrese la referencia del producto">
        <button onclick="buscarReferencia()">Buscar</button>

        <div id="detalle-producto">
            <label for="color">Color:</label>
            <select id="color" name="color">
                <option>Seleccione un color</option>
                <!-- Opciones de color dinámicas -->
            </select>

            <label for="cantidad">Cantidad Devuelta:</label>
            <input type="number" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad a devolver">
        </div>

        <label for="observacion">Motivo de la Devolución:</label>
        <textarea id="observacion" name="observacion" placeholder="Ingrese una observación sobre la devolución"></textarea>

        <button onclick="registrarDevolucion()">Registrar Devolución</button>
    </div>

    <footer class="footer">
        <!-- Información del pie de página igual que en el resto del sistema -->
    </footer>

    <script>
        function buscarReferencia() {
            const referencia = document.getElementById('referencia').value;
            // Llamar a la API para buscar los detalles de la referencia
            $.ajax({
                url: 'buscar_referencia.php',
                type: 'POST',
                data: { referencia },
                success: function(response) {
                    // Llenar los detalles del producto en el formulario
                    $('#color').html(response.colors);
                    $('#detalle-producto').show();
                }
            });
        }

        function registrarDevolucion() {
            const referencia = document.getElementById('referencia').value;
            const color = document.getElementById('color').value;
            const cantidad = document.getElementById('cantidad').value;
            const observacion = document.getElementById('observacion').value;

            // Llamar a la API para registrar la devolución
            $.ajax({
                url: 'registrar_devolucion.php',
                type: 'POST',
                data: {
                    referencia,
                    color,
                    cantidad,
                    observacion
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Devolución registrada', 'La devolución se ha procesado correctamente', 'success');
                    } else {
                        Swal.fire('Error', 'Hubo un problema al registrar la devolución', 'error');
                    }
                }
            });
        }
    </script>
</body>
</html>
