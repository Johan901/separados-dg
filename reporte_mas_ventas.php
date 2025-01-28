<?php
include 'config.php';

// Recibimos las fechas desde el formulario
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

// Inicializamos el array de parámetros
$params = [];

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
        // Filtrar por rango de fechas de inicio y fin del mes
        $inicio_mes = $mes . '-01';  // Primer día del mes
        $fin_mes = $mes . '-31';     // Último día del mes
        $query .= " AND p.fecha_pedido BETWEEN ? AND ?";
        $params[] = $inicio_mes;
        $params[] = $fin_mes;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de mes incorrecto. Use el formato YYYY-MM.'
        ]);
        exit;
    }
}

// Agrupar y ordenar resultados
$query .= " GROUP BY dp.ref, dp.color ORDER BY cantidad DESC LIMIT 10";

try {
    // Depuración: mostrar la consulta y los parámetros
    echo "Consulta SQL: " . $query . "<br>";
    echo "Parámetros: " . print_r($params, true) . "<br>";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $referencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($referencias)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos para la fecha seleccionada.'
        ]);
        exit;
    }

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
