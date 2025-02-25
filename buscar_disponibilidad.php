<?php
include('config.php');
header('Content-Type: application/json');

if (isset($_POST['referencia'])) {
    $ref = $_POST['referencia'];

    // Consulta para obtener la cantidad disponible por color
    $stmt = $conn->prepare("SELECT ref, color, cantidad FROM INVENTARIO WHERE ref = :ref");
    $stmt->bindParam(':ref', $ref);
    $stmt->execute();

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultado) {
        echo json_encode($resultado);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(["error" => "Referencia no proporcionada."]);
}
?>
