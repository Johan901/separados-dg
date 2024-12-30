<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_pedido = $data['id_pedido'];

// Obtener todos los detalles del pedido para ajustar el inventario
$query_detalles = "SELECT cantidad, ref, color FROM detalle_pedido WHERE id_pedido = ?";
$stmt_detalles = $conn->prepare($query_detalles);
$stmt_detalles->execute([$id_pedido]);
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

// Devolver cada referencia al inventario
foreach ($detalles as $detalle) {
    $cantidad = $detalle['cantidad'];
    $ref = $detalle['ref'];
    $color = $detalle['color'];
    $query_update_inventario = "UPDATE inventario SET cantidad = cantidad + ? WHERE ref = ? AND color = ?";
    $stmt_update_inventario = $conn->prepare($query_update_inventario);
    $stmt_update_inventario->execute([$cantidad, $ref, $color]);
}

// Eliminar todos los detalles del pedido
$query_delete = "DELETE FROM detalle_pedido WHERE id_pedido = ?";
$stmt_delete = $conn->prepare($query_delete);
$stmt_delete->execute([$id_pedido]);

// Cambiar estado del pedido a 'eliminado'
$query_update_pedido = "UPDATE pedidos SET estado = 'eliminado' WHERE id_pedido = ?";
$stmt_update_pedido = $conn->prepare($query_update_pedido);
$stmt_update_pedido->execute([$id_pedido]);

// Verificar si el DELETE y el UPDATE fueron exitosos
if ($stmt_delete->rowCount() > 0 && $stmt_update_pedido->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Pedido eliminado correctamente y stock ajustado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el pedido o ajustar el inventario']);
}
?>
