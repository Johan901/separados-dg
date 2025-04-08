<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

header('Content-Type: application/json');

try {
    // Solo actualiza si hay registros con estado incorrecto
    $checkQuery = "
        SELECT COUNT(*) as total
        FROM pedidos
        WHERE estado != 'eliminado' AND (
            (fecha_limite > CURRENT_TIMESTAMP AND estado != 'abierto') OR
            (fecha_limite <= CURRENT_TIMESTAMP AND estado != 'cerrado')
        )
    ";

    $stmt = $conn->prepare($checkQuery);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        // Solo si hay cambios necesarios, hace el UPDATE
        $updateQuery = "
            UPDATE pedidos
            SET estado = CASE
                WHEN fecha_limite > CURRENT_TIMESTAMP AND estado != 'eliminado' THEN 'abierto'
                WHEN fecha_limite <= CURRENT_TIMESTAMP AND estado != 'eliminado' THEN 'cerrado'
                ELSE estado
            END
            WHERE estado != 'eliminado'
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute();
    }

    // Luego, obtiene los estados actuales
    $query = "SELECT id_pedido, estado FROM pedidos WHERE estado != 'eliminado'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
