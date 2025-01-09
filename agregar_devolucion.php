<?php
// Conexión a la base de datos
require 'config.php'; // Asegúrate de que este archivo tenga las credenciales correctas

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referencia = $_POST['referencia'] ?? null;
    $color = $_POST['color'] ?? null;
    $tipo_prenda = $_POST['tipo_prenda'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $observacion = $_POST['observacion'] ?? null;

    if (!$referencia || !$color || !$tipo_prenda || !$cantidad || !$observacion || !is_numeric($cantidad)) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos']);
        exit();
    }

    $cantidad = (int)$cantidad;

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Verificar existencia del producto
        $query = $conn->prepare("SELECT cantidad FROM inventario WHERE referencia = :referencia AND color = :color");
        $query->execute(['referencia' => $referencia, 'color' => $color]);
        $producto = $query->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            exit();
        }

        if ($producto['cantidad'] < $cantidad) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            exit();
        }

        // Actualizar la cantidad en inventario
        $nuevaCantidad = $producto['cantidad'] - $cantidad;
        $updateQuery = $conn->prepare("UPDATE inventario SET cantidad = :cantidad, obvs = :observacion WHERE referencia = :referencia AND color = :color");
        $updateQuery->execute([
            'cantidad' => $nuevaCantidad,
            'observacion' => $observacion,
            'referencia' => $referencia,
            'color' => $color,
        ]);

        // Confirmar transacción
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error en el servidor']);
    }
}
