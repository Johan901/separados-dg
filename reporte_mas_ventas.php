<?php
include 'config.php';

$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

$query = "SELECT dp.ref, dp.color, SUM(dp.cantidad) AS cantidad
          FROM detalle_pedido dp
          JOIN pedidos p ON dp.id_pedido = p.id_pedido
          WHERE 1=1";
$params = [];

// Si se ha seleccionado un día, filtramos por fecha exacta
if (!empty($dia)) {
    $query .= " AND DATE(p.fecha_pedido) = ?";
    $params[] = $dia;
}

// Si se ha seleccionado un mes, filtramos por año-mes
if (!empty($mes)) {
    $query .= " AND TO_CHAR(p.fecha_pedido, 'YYYY-MM') = ?";
    $params[] = $mes;
}

$query .= " GROUP BY dp.ref, dp.color ORDER BY cantidad DESC LIMIT 15";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $referencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($referencias)) {
        echo json_encode([
            'success' => true,
            'referencias' => $referencias
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos para la fecha seleccionada.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta: ' . $e->getMessage()
    ]);
}
?>
