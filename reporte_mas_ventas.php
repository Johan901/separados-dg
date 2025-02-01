<?php
include 'config.php';

// Recibimos las fechas desde el formulario
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

// Inicializar array de parámetros
$params = [];

// Construir la consulta SQL
$query = "SELECT dp.ref, dp.color, SUM(dp.cantidad) AS cantidad
          FROM detalle_pedido dp
          JOIN pedidos p ON dp.id_pedido = p.id_pedido
          WHERE 1=1";

// Si se ha seleccionado un día, filtramos por fecha exacta
if (!empty($dia)) {
    $query .= " AND DATE(p.fecha_pedido) = ?";
    $params[] = $dia;
} elseif (!empty($mes) && strlen($mes) === 7) {
    // Filtrar por mes completo
    $query .= " AND DATE(p.fecha_pedido) LIKE ?";
    $params[] = "$mes%";
}

// Agregar agrupación y orden
$query .= " GROUP BY dp.ref, dp.color ORDER BY cantidad DESC LIMIT 10";

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
