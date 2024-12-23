<?php
include('config.php');

$data = json_decode(file_get_contents("php://input"), true);
$cedula = $data['cedula'] ?? null;

if (!$cedula) {
    echo json_encode(['error' => 'Cédula no proporcionada']);
    exit;
}

try {
    // Verificar si hay un pedido abierto en la tabla orden_separado
    $stmt = $conn->prepare("SELECT cliente_cedula FROM orden_separado WHERE cliente_cedula = :cedula AND estado = 'abierto' LIMIT 1");
    $stmt->bindParam(':cedula', $cedula);
    $stmt->execute();
    $openOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($openOrder) {
        // Buscar la fecha_limite en la tabla pedidos usando la cédula del cliente
        $stmt = $conn->prepare("SELECT fecha_limite FROM pedidos WHERE cliente_cedula = :cedula ORDER BY fecha_limite DESC LIMIT 1");
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['fecha_limite' => $result['fecha_limite'] ?? null]);
    } else {
        echo json_encode(['fecha_limite' => null]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
