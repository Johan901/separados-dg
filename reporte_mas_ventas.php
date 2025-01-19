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
if ($dia) {
    $query .= " AND DATE(p.fecha_pedido) = ?";
    $params = [$dia];
} elseif ($mes) {
    // Si se ha seleccionado un mes, filtramos por el mes
    $query .= " AND DATE_FORMAT(p.fecha_pedido, '%Y-%m') = ?";
    $params = [$mes];
} else {
    // Si no se selecciona ni un día ni un mes, mostramos todas las ventas
    $params = [];
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
