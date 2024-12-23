<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'])) {
    $id_pedido = $_POST['id_pedido'];
    
    // Actualizar el estado del pedido a 'cerrado'
    $query = "UPDATE pedidos SET estado = 'cerrado' WHERE id_pedido = :id_pedido";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_pedido', $id_pedido);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
