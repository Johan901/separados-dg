<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

// Asegúrate de que la respuesta sea JSON
header('Content-Type: application/json');

try {
    // Actualizar los estados de los pedidos
    $updateQuery = "
        UPDATE pedidos
    SET estado = CASE
        WHEN fecha_limite > CURRENT_TIMESTAMP AND estado != 'eliminado' THEN 'abierto'
        WHEN fecha_limite <= CURRENT_TIMESTAMP AND estado != 'eliminado' THEN 'cerrado'
        ELSE estado
    END";

    $stmt = $conn->prepare($updateQuery);
    $stmt->execute();

    // Obtener los nuevos estados de los pedidos
    $query = "SELECT id_pedido, estado FROM pedidos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    // En caso de error, devuelvo un mensaje de error en formato JSON
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Manejo de otros errores
    echo json_encode(['error' => 'Error general: ' . $e->getMessage()]);
}
?>
