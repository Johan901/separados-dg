<?php
session_start();
include('config.php');

$cedula = $_POST['cedula'] ?? null;

if ($cedula) {
    // Verifica si ya existe una orden cerrada
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orden_separado WHERE cliente_cedula = ? AND estado = 'cerrado'");
    $stmt->execute([$cedula]);
    $count = $stmt->fetchColumn();

    // Si hay una orden cerrada, actualiza su estado a "abierto"
    if ($count > 0) {
        $updateStmt = $conn->prepare("UPDATE orden_separado SET estado = 'abierto' WHERE cliente_cedula = ? AND estado = 'cerrado'");
        if ($updateStmt->execute([$cedula])) {
            echo json_encode(['success' => true, 'message' => 'Orden existente actualizada a abierta.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la orden existente.']);
        }
    } else {
        // Si no hay órdenes cerradas, crea una nueva orden
        $insertStmt = $conn->prepare("INSERT INTO orden_separado (cliente_cedula, estado) VALUES (?, 'abierto')");
        if ($insertStmt->execute([$cedula])) {
            echo json_encode(['success' => true, 'message' => 'Se ha creado una nueva orden separada.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo crear la orden separada.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Cédula no proporcionada.']);
}
?>
