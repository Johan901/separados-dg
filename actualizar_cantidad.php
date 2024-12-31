<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_detalle = $data['id_detalle'];
$nueva_cantidad = $data['nueva_cantidad'];

// Obtener el detalle del pedido (precio unitario y cantidad anterior)
$query_detalle = "SELECT precio_unitario, cantidad, ref, color FROM detalle_pedido WHERE id_detalle = ?";
$stmt_detalle = $conn->prepare($query_detalle);
$stmt_detalle->execute([$id_detalle]);
$detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC);

$precio_unitario = $detalle['precio_unitario'];
$cantidad_anterior = $detalle['cantidad'];
$ref = $detalle['ref'];
$color = $detalle['color'];

// Calcular el nuevo subtotal
$nuevo_subtotal = $precio_unitario * $nueva_cantidad;

// Actualizar la cantidad y el subtotal en detalle_pedido
$query_update_subtotal = "UPDATE detalle_pedido SET cantidad = ?, subtotal = ? WHERE id_detalle = ?";
$stmt_update_subtotal = $conn->prepare($query_update_subtotal);
$stmt_update_subtotal->execute([$nueva_cantidad, $nuevo_subtotal, $id_detalle]);

// Calcular la diferencia para ajustar el inventario
$diferencia = $nueva_cantidad - $cantidad_anterior;

// Actualizar el inventario del producto basado en la diferencia
$query_update_inventario = "UPDATE inventario SET cantidad = cantidad - ? WHERE ref = ? AND color = ?";
$stmt_update_inventario = $conn->prepare($query_update_inventario);
$stmt_update_inventario->execute([$diferencia, $ref, $color]);

// Recalcular el total del pedido
$query_total_pedido = "SELECT SUM(subtotal) AS total FROM detalle_pedido WHERE id_pedido = (SELECT id_pedido FROM detalle_pedido WHERE id_detalle = ?)";
$stmt_total = $conn->prepare($query_total_pedido);
$stmt_total->execute([$id_detalle]);
$total = $stmt_total->fetchColumn();

// Actualizar el total en la tabla pedidos
$query_update_total = "UPDATE pedidos SET total_pedido = ? WHERE id_pedido = (SELECT id_pedido FROM detalle_pedido WHERE id_detalle = ?)";
$stmt_update_total = $conn->prepare($query_update_total);
$stmt_update_total->execute([$total, $id_detalle]);

if ($stmt_update_total->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Cantidad y subtotal actualizados, total del pedido ajustado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Cantidad y subtotal actualizados, pero no se pudo actualizar el total del pedido']);
}
?>
