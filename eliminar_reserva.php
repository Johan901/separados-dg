<?php
require 'config.php';

$ref = $_POST['ref'];
$color = $_POST['color'];
$cantidad = intval($_POST['cantidad']);
$cedula = $_POST['cedula'];

try {
    // 1. Devolver la cantidad al inventario
    $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad + :cantidad WHERE ref = :ref AND color = :color");
    $stmt->execute(['cantidad' => $cantidad, 'ref' => $ref, 'color' => $color]);

    // 2. Eliminar una reserva (la más antigua)
    $stmt = $conn->prepare("
        DELETE FROM reservas_temporales 
        WHERE id = (
            SELECT id FROM reservas_temporales 
            WHERE ref = :ref AND color = :color AND cedula_cliente = :cedula 
            ORDER BY fecha_reserva ASC
            LIMIT 1
        )
    ");
    $stmt->execute(['ref' => $ref, 'color' => $color, 'cedula' => $cedula]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al eliminar reserva']);
}
?>
