<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php');
header('Content-Type: application/json');
include('config.php');

$referencia = $_POST['ref'];
$color = $_POST['color'];
$cantidad = $_POST['cantidad'];

if (!$referencia || !$color || !$cantidad) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Actualizar inventario
$query = "UPDATE inventario SET cantidad = cantidad - ? WHERE referencia = ? AND color = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('iss', $cantidad, $referencia, $color);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el inventario']);
}
?>
