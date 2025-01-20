<?php
include 'config.php';

// Recibimos las fechas desde el formulario
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

// Creamos la consulta para obtener las referencias más vendidas
$query = "SELECT dp.ref, dp.color, SUM(dp.cantidad) AS cantidad
          FROM detalle_pedido dp
          JOIN pedidos p ON dp.id_pedido = p.id_pedido
          WHERE 1=1";

// Si se ha seleccionado un día, filtramos por fecha
if (!empty($dia)) {
    $query .= " AND DATE(p.fecha_pedido) = ?";
    $params[] = $dia;
} elseif (!empty($mes)) {
    // Asegurar que el formato del mes es correcto antes de la consulta
    if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
        $query .= " AND DATE_FORMAT(p.fecha_pedido, '%Y-%m') = ?";
        $params[] = $mes;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de mes incorrecto.'
        ]);
        exit;
    }
}


$query .= " GROUP BY dp.ref, dp.color ORDER BY cantidad DESC LIMIT 10";  // Limitar a las 10 referencias más vendidas

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $referencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar la respuesta en formato JSON
    echo json_encode([
        'success' => true,
        'referencias' => $referencias
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
