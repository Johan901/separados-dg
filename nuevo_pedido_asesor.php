<?php
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
    <title>Panel de Administración - DG</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/styles_agregar_pedidos.css?v=2.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        .section-title {
            margin: 20px 0; /* Espaciado mejorado para los títulos de sección */
            font-weight: bold;
            font-size: 1.2em;
        }
        .input-group {
            margin: 10px 0; /* Espaciado para los grupos de entrada */
        }
    </style>
</head>

<header>
    <div class="hamburger-menu">
        <i class="fas fa-bars"></i>
        <div class="dropdown-menu">
            <a href="asesor_panel.php">Inicio</a>
            <a href="agregar_usuario_asesor.php">Agregar nuevo cliente</a>
            <a href="nuevo_pedido_asesor.php">Agregar nuevo pedido</a>
            <a href="historial_asesor.php">Historial de pedidos</a>
        </div>
    </div>
    
    <a href="asesor_panel.php" class="logo">Dulce Guadalupe</a>
    <a href="logout.php" class="logout-button">Cerrar Sesión</a>
</header>

<body>
    <div class="container">
        <h1>Crear Nuevo Pedido</h1>
        
        <!-- Información del Cliente -->
        <label for="cedula">Cédula del Cliente:</label>
        <div style="padding: 10px 0px 5px 10px;" class="input-group">
            <input type="text" id="cedula" name="cedula" placeholder="Ingrese la cédula del cliente">
            <button class="boton-buscar" type="button" onclick="buscarCliente()">Buscar</button>
        </div>

        <div style="padding: 10px 0px 5px 10px;" class="input-group">
        </div>

        <label for="nombre">Nombre del Cliente:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre del cliente" readonly>

        <label for="estado"></label>

        <!-- Búsqueda de Referencia -->
        <div class="section-title">Detalle del Pedido</div>
        <label for="referencia-busqueda">Buscar Referencia:</label>
        <div class="input-group">
            <input type="text" id="referencia-busqueda" name="referencia" placeholder="Ingrese la referencia">
            <button class="boton-buscar" type="button" onclick="buscarReferencia()">Buscar</button>
        </div>

        <!-- Selección de tipo de compra -->
<label for="tipo-compra">Tipo de Compra:</label>
<div>
    <button class="boton-buscar" type="button" id="botonMayor" onclick="seleccionarMayor()">Al por Mayor</button>
    <button class="boton-buscar" type="button" id="botonDetal" onclick="seleccionarDetal()">Al Detal</button>
</div>

        <label for="color">Color:</label>
        <select id="color" name="color">
            <option>Seleccione un color</option>
        </select>

        <label for="tipo-prenda">Tipo de Prenda:</label>
        <input type="text" id="tipo-prenda" name="tipo-prenda" readonly>

        <label for="precio-unitario">Precio Unitario:</label>
        <input type="text" id="precio-unitario" name="precio-unitario" readonly>

        <label for="">Cantidad:</label>
        <input type="text" id="cantidad" name="cantidad" placeholder="Ingrese cantidad" onchange="calcularSubtotal()" required>

        

        <button class="boton-buscar" onclick="agregarProducto()">Agregar Producto</button>

        <div class="section-title">Productos en el Pedido</div>
        
        <!-- Productos en el Pedido -->
        <table id="productos">
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Color</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se agregarán los productos -->
            </tbody>
        </table>

        <label for="asesor">Línea:</label>
        <select id="asesor" name="asesor">
            <option>Seleccione una Línea</option>
            <option value="3153925613">3153925613</option>
            <option value="3183925613">3183925613</option>
            <option value="3103925613">3103925613</option>
        </select>


        <!-- Dropdown para medio de conocimiento -->
        <label for="medio_conocimiento">¿Cómo conoció a Dulce Guadalupe?:*</label>
        <select id="medio_conocimiento" name="medio_conocimiento" required>
            <option>Seleccione un medio</option>
            <option value="Pauta Publicitaria">Pauta Publicitaria</option>
            <option value="Redes Sociales">Redes Sociales</option>
            <option value="Cliente Frecuente">Cliente Frecuente</option>
            <option value="Punto Físico">Punto Físico</option>
            <option value="Boca a Boca">Boca a Boca</option>
            <option value="Email Marketing">Email Marketing</option>
            <option value="Eventos o Ferias">Eventos o Ferias</option>
            <option value="Promociones en Línea">Promociones en Línea</option>
            <option value="Otros">Otros</option>
        </select>


        <!-- Nueva sección para buscar pedidos -->
        <div class="section-title">Buscar Pedidos</div>

        <div class="input-group">
            <button class="boton-buscar" type="button" onclick="buscarPedidos()">Buscar Pedidos</button>
        </div>

        <table id="tabla-pedidos">
    <thead>
        <tr>
            <th>ID Pedido</th>
            <th>Fecha Pedido</th>
            <th>Fecha Límite</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <!-- Aquí se agregarán los resultados de la búsqueda -->
    </tbody>
</table>    

        <label for="envio">Envío:</label>
        <input type="text" id="envio" name="envio" placeholder="Dirección de envío">

        <label for="fecha">Fecha del Pedido:</label>
        <input type="datetime-local" id="fecha" name="fecha" required>

        <label for="fechaLimite">Fecha Límite:</label>
        <input type="datetime-local" id="fechaLimite" name="fechaLimite" required>

        <label for="total-pedido">Total del Pedido:</label>
        <input type="text" id="total-pedido" name="total-pedido" placeholder="Total del pedido" disabled>

        <button class="boton-buscar" onclick="crearPedido()">Crear Pedido</button>
    </div>

    <script src="js/main_user.js?v=1.1"></script>
    <script src="pedidos_script.js?v=9.0" defer></script>

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
        &copy; 2024 Dulce Guadalupe | Todos los derechos reservados
    </div>
</footer>

</html>
