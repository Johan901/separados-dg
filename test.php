<?php
// Habilitar la visualización de errores para facilitar la depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir tu conexión a la base de datos
include 'config.php'; // Cambia esto si tu archivo de conexión se llama diferente

// Simular los datos que recibirías desde tu formulario
$cedula = '123456'; // Cambia esto por una cédula válida que estés probando
$total_pedido = 100; // Cambia esto si es necesario
$asesor = 'Asesor Prueba'; // Cambia esto si es necesario
$envio = 'Envio Prueba'; // Cambia esto si es necesario
$productos = json_encode([ // Simular productos
    ['referencia' => 'prod1', 'color' => 'rojo', 'cantidad' => 1, 'precioUnitario' => 50, 'subtotal' => 50],
    ['referencia' => 'prod2', 'color' => 'azul', 'cantidad' => 1, 'precioUnitario' => 50, 'subtotal' => 50]
]);
$fecha_pedido = date("Y-m-d H:i:s"); // Usar la fecha y hora actual

// Verificar si falta algún dato
if (empty($cedula) || empty($total_pedido) || empty($asesor) || empty($envio) || empty($productos) || empty($fecha_pedido)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos en la solicitud']);
    exit;
}

try {
    // Consultar si el cliente tiene un pedido abierto en orden_separado
    $stmt = $conn->prepare("SELECT cliente_cedula FROM orden_separado WHERE cliente_cedula = :cedula AND estado = 'abierto' LIMIT 1");
    $stmt->bindParam(':cedula', $cedula);
    $stmt->execute();
    $openOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mostrar si hay un pedido abierto
    if ($openOrder) {
        echo "El cliente tiene un pedido abierto.<br>";
        // Buscar la fecha límite del pedido abierto en la tabla pedidos
        $stmt = $conn->prepare("SELECT fecha_limite FROM pedidos WHERE cliente_cedula = :cedula ORDER BY fecha_limite DESC LIMIT 1");
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $existingFechaLimite = $stmt->fetch(PDO::FETCH_ASSOC);
        $fecha_limite = $existingFechaLimite['fecha_limite'] ?? null;
        echo "Fecha límite existente: " . ($fecha_limite ? $fecha_limite : "No hay fecha límite"). "<br>";
    } else {
        // Calcular nueva fecha límite si no hay un pedido abierto
        $fecha_limite = date("Y-m-d H:i:s", strtotime($fecha_pedido . ' + 8 days'));
        echo "Calculando nueva fecha límite: " . $fecha_limite . "<br>";
    }

    // Iniciar una transacción
    $conn->beginTransaction();

    // Insertar en la tabla pedidos
    $stmt = $conn->prepare("INSERT INTO pedidos (cliente_cedula, total_pedido, asesor, envio, fecha_pedido, fecha_limite) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cedula, $total_pedido, $asesor, $envio, $fecha_pedido, $fecha_limite]);

    $id_pedido = $conn->lastInsertId();
    echo "ID del pedido registrado: " . $id_pedido . "<br>";

    // Procesar productos y descontar inventario
    $productos = json_decode($productos, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Error en el formato de los productos: ' . json_last_error_msg()]);
        exit;
    }

    $stmt_detalle = $conn->prepare("INSERT INTO detalle_pedido (id_pedido, ref, color, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_inventario = $conn->prepare("UPDATE inventario SET cantidad = cantidad - ? WHERE ref = ? AND color = ?");

    foreach ($productos as $producto) {
        $stmt_detalle->execute([$id_pedido, $producto['referencia'], $producto['color'], $producto['cantidad'], $producto['precioUnitario'], $producto['subtotal']]);
        $stmt_inventario->execute([$producto['cantidad'], $producto['referencia'], $producto['color']]);
    }

    // Confirmar transacción
    $conn->commit();
    echo "Pedido creado y descontado del inventario exitosamente!<br>";
    echo "Fecha límite registrada: " . $fecha_limite . "<br>";

} catch (Exception $e) {
    // Revertir transacción si ocurre un error
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}

$conn = null; // Cerrar la conexión
?>
