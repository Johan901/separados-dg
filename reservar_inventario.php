<?php
include 'config.php';

$ref = $_POST['ref'];
$color = $_POST['color'];
$cantidad = intval($_POST['cantidad']);
$cedula = $_POST['cedula'];

try {
    // 1. Verificar cantidad disponible
    $stmt = $conn->prepare("SELECT cantidad FROM inventario WHERE ref = :ref AND color = :color");
    $stmt->execute(['ref' => $ref, 'color' => $color]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['cantidad'] >= $cantidad) {
        // 2. Descontar inventario
        $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad - :cantidad WHERE ref = :ref AND color = :color");
        $stmt->execute(['cantidad' => $cantidad, 'ref' => $ref, 'color' => $color]);

        // 3. Registrar reserva
        $stmt = $conn->prepare("INSERT INTO reservas_temporales (ref, color, cantidad, cedula_cliente) VALUES (:ref, :color, :cantidad, :cedula)");
        $stmt->execute(['ref' => $ref, 'color' => $color, 'cantidad' => $cantidad, 'cedula' => $cedula]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Inventario insuficiente']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos']);
}
?>
