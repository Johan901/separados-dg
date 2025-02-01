<?php
include 'config.php';

// Recibimos las fechas desde el formulario
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

// Inicializar el array de parámetros
$params = [];

// Construcción de la consulta
$query = "SELECT dp.ref, dp.color, SUM(dp.cantidad) AS cantidad
          FROM detalle_pedido dp
          JOIN pedidos p ON dp.id_pedido = p.id_pedido
          WHERE 1=1";

// Si se ha seleccionado un día, filtramos por fecha exacta
if (!empty($dia)) {
    $query .= " AND DATE(p.fecha_pedido) = ?";
    $params[] = $dia;
} 
// Si se ha seleccionado un mes, filtramos por el mes y año
elseif (!empty($mes)) {
    if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
        $query .= " AND DATE_FORMAT(p.fecha_pedido, '%Y-%m') = ?";
        $params[] = $mes;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de mes incorrecto. Debe ser YYYY-MM.'
        ]);
        exit;
    }
}

// Agrupar por referencia y color, ordenar por cantidad vendida
$query .= " GROUP BY dp.ref, dp.color ORDER BY cantidad DESC LIMIT 10";

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
