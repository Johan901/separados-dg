<?php
require 'config.php';

$ref = $_POST['ref'];
$color = $_POST['color'];
$cantidad = intval($_POST['cantidad']);
$cedula = $_POST['cedula'];

try {
    // 1. Verificar cantidad actual
    $stmt = $conn->prepare("SELECT cantidad FROM inventario WHERE ref = :ref AND color = :color");
    $stmt->execute(['ref' => $ref, 'color' => $color]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['error' => 'Referencia no encontrada']);
        exit;
    }

    $disponible = intval($row['cantidad']);

    if ($disponible < $cantidad) {
        echo json_encode(['error' => "Solo hay $disponible unidades disponibles"]);
        exit;
    }

    // 2. Descontar del inventario
    $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad - :cantidad WHERE ref = :ref AND color = :color");
    $stmt->execute(['cantidad' => $cantidad, 'ref' => $ref, 'color' => $color]);

    // 3. Insertar reserva
    $stmt = $conn->prepare("INSERT INTO reservas_temporales (ref, color, cantidad, cedula_cliente) VALUES (:ref, :color, :cantidad, :cedula)");
    $stmt->execute(['ref' => $ref, 'color' => $color, 'cantidad' => $cantidad, 'cedula' => $cedula]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al reservar inventario']);
}
?>
