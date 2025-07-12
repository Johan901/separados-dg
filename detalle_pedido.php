<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - DG</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=2.1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="header-right">
                <h2><label for="id_pedido">Remitente: ÁNGELA MARCELA TASCÓN</label></h2>
        </div>
        <a href="logout.php" class="logout-button">Cerrar Sesión</a>
    </header>

    <h2>Detalle de Pedido</h2>
    
    <?php
include 'config.php';

// Verifica si el usuario ha iniciado sesión
session_start(); 
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Verifica si se ha enviado el ID del pedido
if (isset($_GET['id'])) {
    $id_pedido = $_GET['id'];

    // Actualizar la consulta SQL para incluir las nuevas columnas
    $query = "SELECT dp.id_detalle, dp.id_pedido, dp.ref, dp.color, dp.cantidad, dp.precio_unitario, 
                 dp.subtotal, dp.actualizado, 
                 c.cedula, c.nombre, c.telefono,
                 p.asesor, p.envio
          FROM detalle_pedido dp
          INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
          INNER JOIN clientes c ON p.cliente_cedula = c.cedula
          WHERE dp.id_pedido = :id_pedido";


    // Preparar la consulta
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            echo "<table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Referencia</th>
                    <th>Color</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (COP)</th>
                    <th>Subtotal (COP)</th>
                    <th>Celular Cliente</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Asesor</th>
                    <th>Envío</th>
                    <th>Separar</th>
                </tr>";

            foreach ($result as $row) {
                $id_detalle = $row['id_detalle'];
                $estado_separado = $row['actualizado'] == 1 ? "Ya separado" : "Pendiente de separar";

                echo "<tr>
                        <td>{$row['id_pedido']}</td>
                        <td>{$row['ref']}</td>
                        <td>{$row['color']}</td>
                        <td>{$row['cantidad']}</td>
                        <td>" . '$' . number_format($row['precio_unitario'], 0, '', '.') . "</td>
                        <td>" . '$' . number_format($row['subtotal'], 0, '', '.') . "</td>
                        <td>{$row['telefono']}</td>
                        <td>{$row['cedula']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['asesor']}</td>
                        <td>{$row['envio']}</td>
                        <td>
                            <form method='POST' action='detalle_pedido.php'>
                                <input type='hidden' name='id_detalle' value='{$id_detalle}'>
                                <input type='hidden' name='id_pedido' value='{$row['id_pedido']}'>
                                <button class='button' type='submit' name='marcar_separado' " . ($row['actualizado'] == 1 ? 'disabled' : '') . ">Marcar como separado</button>
                            </form>
                            <span class='estado-separado'>{$estado_separado}</span>
                        </td>
                    </tr>";
            }
            echo "</table>";

            // Obtener y mostrar el total del pedido
            $query_total = "SELECT total_pedido FROM pedidos WHERE id_pedido = :id_pedido";
            $stmt_total = $conn->prepare($query_total);
            $stmt_total->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt_total->execute();
            $total_result = $stmt_total->fetch(PDO::FETCH_ASSOC);

            if ($total_result) {
                $total_pedido = $total_result['total_pedido'];
                echo "<div id='total-pedido' style='text-align:center; margin-top: 20px; font-size: 18px;'>
                    <strong>Total del Pedido:</strong> $" . number_format($total_pedido, 0, '', '.') . "
                </div>";

                // Agrega el botón justo después del total
                echo "<div style='text-align:center; margin-top:20px; margin-bottom:60px;'>
                    <button class='button' onclick='imprimirSoloTicket()'>🖨️ Imprimir Ticket</button>
                </div>";


            }
        } else {
            echo "<p>No se encontraron detalles para el ID de pedido proporcionado.</p>";
        }  
    } catch (PDOException $e) {
        // Mostrar error en la consola del navegador
        $error_message = json_encode($e->getMessage(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        echo "<script>
                console.error('Error en la consulta: ' + $error_message);
              </script>";
        echo "<p>Ocurrió un error al procesar la solicitud. Por favor, revisa la consola para más detalles.</p>";
    }
} else {
    echo "<p>Por favor, proporciona un ID de pedido para buscar.</p>";
}

if (isset($_POST['marcar_separado'])) {
    $id_pedido = $_POST['id_pedido'];  
    $id_detalle = $_POST['id_detalle']; 

    $query_update = "UPDATE detalle_pedido SET actualizado = TRUE WHERE id_detalle = :id_detalle";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bindValue(':id_detalle', $id_detalle, PDO::PARAM_INT);
    $stmt_update->execute();

    $query_separado_check = "SELECT COUNT(*) AS total, 
                                    SUM(CASE WHEN actualizado = FALSE THEN 1 ELSE 0 END) AS pendientes
                             FROM detalle_pedido
                             WHERE id_pedido = :id_pedido";
    $stmt_separado_check = $conn->prepare($query_separado_check);
    $stmt_separado_check->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt_separado_check->execute();
    $result_separado_check = $stmt_separado_check->fetch(PDO::FETCH_ASSOC);

 
    if ($result_separado_check['pendientes'] == 0) { 
        $query_update_pedido = "UPDATE pedidos SET pedido_separado = TRUE WHERE id_pedido = :id_pedido";
        $stmt_update_pedido = $conn->prepare($query_update_pedido);
        $stmt_update_pedido->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt_update_pedido->execute(); 
    }

    ob_clean(); // Limpia cualquier salida previa
    header('Location: historial_pedidos.php');
    exit();  
}



?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  window.imprimirSoloTicket = function () {
    const filas = document.querySelectorAll("table tr");
    const total = document.getElementById("total-pedido");

    if (!filas || filas.length < 2 || !total) {
      alert("⚠️ No se encontró la tabla o el total del pedido.");
      return;
    }

    let nombre = "", cedula = "", productosHTML = "";

    // Iterar desde la segunda fila (índice 1) porque la primera es el encabezado
    for (let i = 1; i < filas.length; i++) {
      const celdas = filas[i].querySelectorAll("td");
      if (celdas.length < 9) continue;

      const id = celdas[0].textContent.trim();
      const ref = celdas[1].textContent.trim();
      const color = celdas[2].textContent.trim();
      const cantidad = celdas[3].textContent.trim();
      cedula = celdas[7].textContent.trim();
      nombre = celdas[8].textContent.trim();

      productosHTML += `<tr>
                          <td>${id}</td>
                          <td>${ref}</td>
                          <td>${color}</td>
                          <td>${cantidad}</td>
                        </tr>`;
    }

    const totalHTML = total.outerHTML;

    const contenidoTicket = `
      <html>
        <head>
          <title>Ticket Dulce Guadalupe</title>
          <style>
            @page { size: 80mm auto; margin: 5mm; }
            body {
              width: 80mm;
              font-family: 'Poppins', sans-serif;
              font-size: 9pt;
              color: #000;
              background: #fff;
              text-align: left;
            }
            h2 {
              font-size: 14pt;
              text-align: center;
              margin-bottom: 10px;
            }
            .datos-cliente, .productos {
              margin: 5px 0;
            }
            table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 5px;
              margin-bottom: 10px;
            }
            th, td {
              border-bottom: 1px dashed #000;
              padding: 3px;
              font-size: 9pt;
              text-align: left;
            }
            tr:last-child td {
              border-bottom: none;
            }
            .footer-impresion {
              margin-top: 20px;
              font-size: 8pt;
              line-height: 1.4;
              text-align: center;
              border-top: 1px dashed #000;
              padding-top: 10px;
            }
          </style>
        </head>
        <body>
          <h2>Dulce Guadalupe</h2>

          <div class="datos-cliente">
            <strong>Datos del Cliente:</strong><br>
            Nombre: ${nombre}<br>
            Cédula: ${cedula}
          </div>

          <div class="productos">
            <strong>Productos:</strong>
            <table>
              <tr><th>ID</th><th>Ref</th><th>Color</th><th>Cant</th></tr>
              ${productosHTML}
            </table>
          </div>

          ${totalHTML}

          <div class="footer-impresion">
            © 2025 Dulce Guadalupe. Todos los derechos reservados.<br>
            Cali - Colombia<br>
            C.C La Casona - Cra. 6 #12-61 Local 302<br>
            <strong>Equipo de Tecnología DG</strong>
          </div>
        </body>
      </html>
    `;

    const ventana = window.open('', '', 'width=400,height=600');
    ventana.document.write(contenidoTicket);
    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
  };
});
</script>


<script src="js/main_user.js?v=1.1"></script>

</body>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about"> Iframe we get_class getter GROUP BY p.id_pedido, c.nombre
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