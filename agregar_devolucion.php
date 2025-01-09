<?php
require 'config.php'; // Archivo con la conexión a la base de datos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referencia = $_POST['referencia'];
    $color = $_POST['color'];
    $cantidad = intval($_POST['cantidad']);
    $observacion = $_POST['observacion'];

    try {
        $conexion->beginTransaction();

        // 1. Reducir la cantidad en la tabla `inventario`
        $queryInventario = "UPDATE inventario 
                            SET cantidad = cantidad - :cantidad 
                            WHERE ref = :referencia AND color = :color AND cantidad >= :cantidad";
        $stmtInventario = $conexion->prepare($queryInventario);
        $stmtInventario->execute([
            ':cantidad' => $cantidad,
            ':referencia' => $referencia,
            ':color' => $color,
        ]);

        if ($stmtInventario->rowCount() === 0) {
            throw new Exception('No se pudo reducir la cantidad. Verifique la referencia, el color o si hay suficiente inventario.');
        }

        // 2. Insertar en la tabla `devoluciones`
        $queryDevoluciones = "INSERT INTO devoluciones (ref, color, cantidad, obvs) 
                              VALUES (:referencia, :color, :cantidad, :observacion)";
        $stmtDevoluciones = $conexion->prepare($queryDevoluciones);
        $stmtDevoluciones->execute([
            ':referencia' => $referencia,
            ':color' => $color,
            ':cantidad' => $cantidad,
            ':observacion' => $observacion,
        ]);

        $conexion->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conexion->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
