<?php
include('config.php');
header('Content-Type: application/json');

if (isset($_GET['ref'])) {
    $ref = $_GET['ref'];

    // Consulta para obtener la cantidad disponible de la referencia
    $stmt = $conn->prepare("SELECT SUM(cantidad) AS disponibilidad FROM INVENTARIO WHERE ref = :ref");
    $stmt->bindParam(':ref', $ref);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && $resultado['disponibilidad'] !== null) {
        echo json_encode(["ref" => $ref, "disponibilidad" => $resultado['disponibilidad']]);
    } else {
        echo json_encode(["error" => "Referencia no encontrada o sin stock."]);
    }
} else {
    echo json_encode(["error" => "Referencia no proporcionada."]);
}
?>
