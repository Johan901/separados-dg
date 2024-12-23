<?php
session_start();
include('config.php');

$cedula = $_GET['cedula'] ?? null;

if ($cedula) {
    $stmt = $conn->prepare("SELECT * FROM orden_separado WHERE cliente_cedula = ? AND estado = 'abierto'");
    $stmt->execute([$cedula]);
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($ordenes) {
        echo json_encode(['success' => true, 'ordenes' => $ordenes]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No hay órdenes abiertas para esta cédula.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Cédula no proporcionada.']);
}
?>
