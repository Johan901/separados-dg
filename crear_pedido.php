<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php');
header('Content-Type: application/json');

// Recibir datos del formulario
$cedula = $_POST['cedula'] ?? null;
$total_pedido = $_POST['total'] ?? null;
$asesor = $_POST['asesor'] ?? null;
$envio = $_POST['envio'] ?? null;
$productos = $_POST['productos'] ?? null;
$fecha_pedido = $_POST['fecha'] ?? null;
$fecha_limite = $_POST['fechaLimite'] ?? null;
$medio_conocimiento = $_POST['medio_conocimiento'] ?? null;

if (empty($cedula) || empty($total_pedido) || empty($asesor) || empty($envio) || empty($productos) || empty($fecha_pedido) || empty($medio_conocimiento)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos en la solicitud']);
    exit;
}

// Formatear la fecha del pedido
if ($fecha_pedido) {
    $fecha_pedido = date("Y-m-d H:i:s", strtotime($fecha_pedido));
}

if ($fecha_limite) {
    $fecha_limite = date("Y-m-d H:i:s", strtotime($fecha_limite));
}

try {
    $conn->beginTransaction();

    // Verificar si existe un pedido abierto para el cliente
    $stmt = $conn->prepare("SELECT id_pedido, total_pedido FROM pedidos WHERE cliente_cedula = ? AND estado = 'abierto'");
    $stmt->execute([$cedula]);
    $pedidoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pedidoExistente) {
        // Si existe un pedido abierto, actualizar el total del pedido sumando el nuevo total
        $nuevoTotal = $pedidoExistente['total_pedido'] + $total_pedido;
        $stmt = $conn->prepare("UPDATE pedidos SET total_pedido = ?, fecha_limite = ? WHERE id_pedido = ?");
        $stmt->execute([$nuevoTotal, $fecha_limite, $pedidoExistente['id_pedido']]);

        $pedidoId = $pedidoExistente['id_pedido'];

        // Marcar todos los productos del pedido como 'actualizado'
        $stmt_update = $conn->prepare("UPDATE detalle_pedido SET actualizado = TRUE WHERE id_pedido = ?");
        $stmt_update->execute([$pedidoId]);
    } else {
        // Si no hay pedido abierto, crear un nuevo pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (cliente_cedula, total_pedido, asesor, envio, medio_conocimiento, fecha_pedido, fecha_limite, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'abierto')");
        $stmt->execute([$cedula, $total_pedido, $asesor, $envio, $medio_conocimiento, $fecha_pedido, $fecha_limite]);

        $pedidoId = $conn->lastInsertId();
    }

    // Insertar los productos en la tabla de detalle de pedidos
    $productos = json_decode($productos, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'error' => 'Error en el formato de los productos: ' . json_last_error_msg()]);
        exit;
    }

    $stmt_detalle = $conn->prepare("INSERT INTO detalle_pedido (id_pedido, ref, color, cantidad, precio_unitario, subtotal, actualizado) VALUES (?, ?, ?, ?, ?, ?, FALSE)");

foreach ($productos as $producto) {
    $stmt_detalle->execute([
        $pedidoId,
        $producto['referencia'],
        $producto['color'],
        $producto['cantidad'],
        $producto['precioUnitario'],
        $producto['subtotal']
    ]);

    // Ahora, descontamos la cantidad de productos del inventario
    $stmt_inventario = $conn->prepare("UPDATE inventario SET cantidad = cantidad - ? WHERE referencia = ? AND color = ?");
    $stmt_inventario->execute([$producto['cantidad'], $producto['referencia'], $producto['color']]);
}

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Pedido procesado correctamente']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error al procesar el pedido: ' . $e->getMessage()]);
}
?>
