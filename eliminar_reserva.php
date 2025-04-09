<?php
require 'config.php';

$ref = $_POST['ref'];
$color = $_POST['color'];
$cantidad = intval($_POST['cantidad']);
$cedula = $_POST['cedula'];

try {
    // 1. Devolver al inventario
    $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad + :cantidad WHERE ref = :ref AND color = :color");
    $stmt->execute(['cantidad' => $cantidad, 'ref' => $ref, 'color' => $color]);

    // 2. Eliminar solo UNA fila con CTE
    $deleteSql = "
        WITH fila AS (
            SELECT id FROM reservas_temporales
            WHERE ref = :ref AND color = :color AND cedula_cliente = :cedula
            ORDER BY fecha_reserva ASC
            LIMIT 1
        )
        DELETE FROM reservas_temporales
        WHERE id IN (SELECT id FROM fila)
    ";

    $stmtDel = $conn->prepare($deleteSql);
    $stmtDel->execute(['ref' => $ref, 'color' => $color, 'cedula' => $cedula]);

    // Validar si realmente se eliminó algo
    if ($stmtDel->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No se encontró la reserva para eliminar']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al eliminar reserva']);
}
?>
