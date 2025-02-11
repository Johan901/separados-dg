<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

if (isset($_GET['id_detalle']) && isset($_GET['id_pedido'])) {
    $id_detalle = $_GET['id_detalle'];
    $id_pedido = $_GET['id_pedido'];

    try {
        $conn->beginTransaction();

        // Eliminar el producto del detalle_pedido
        $query = "DELETE FROM detalle_pedido WHERE id_detalle = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_detalle]);

        // Recalcular el total del pedido
        $query_total = "SELECT SUM(subtotal) AS total FROM detalle_pedido WHERE id_pedido = ?";
        $stmt_total = $conn->prepare($query_total);
        $stmt_total->execute([$id_pedido]);
        $result = $stmt_total->fetch(PDO::FETCH_ASSOC);

        $nuevo_total = $result['total'] ?? 0; // Si no hay productos, total = 0

        // Actualizar el total_pedido en la tabla pedidos
        $query_update = "UPDATE pedidos SET total_pedido = ? WHERE id_pedido = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->execute([$nuevo_total, $id_pedido]);

        $conn->commit();
        header("Location: editar_pedido_bodeguero.php?id_pedido=$id_pedido&msg=Producto eliminado y total actualizado");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: editar_pedido_bodeguero.php?id_pedido=$id_pedido&msg=Error al eliminar el producto");
        exit();
    }
} else {
    header("Location: bodeguero_panel.php?msg=ID de producto o pedido no proporcionado");
    exit();
}
?>
