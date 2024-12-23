<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id_detalle = $data['id_detalle'];

// Obtener la cantidad y el producto del detalle antes de eliminarlo
$query_detalle = "SELECT cantidad, ref, color FROM detalle_pedido WHERE id_detalle = ?";
$stmt_detalle = $conn->prepare($query_detalle);
$stmt_detalle->execute([$id_detalle]);
$detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC);

$cantidad = $detalle['cantidad'];
$ref = $detalle['ref'];
$color = $detalle['color'];

// Devolver la cantidad al inventario
$query_update_inventario = "UPDATE inventario SET cantidad = cantidad + ? WHERE ref = ? AND color = ?";
$stmt_update_inventario = $conn->prepare($query_update_inventario);
$stmt_update_inventario->execute([$cantidad, $ref, $color]);

// Eliminar el producto del detalle del pedido
$query = "DELETE FROM detalle_pedido WHERE id_detalle = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_detalle]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado y stock ajustado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
}
?>
