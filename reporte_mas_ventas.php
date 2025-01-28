<?php
include 'config.php';

// Recibimos las fechas desde el formulario
$dia = isset($_POST['dia']) ? $_POST['dia'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

// Depuración: Mostrar los valores recibidos
var_dump($dia, $mes);

// Inicializamos el array de parámetros
$params = [];

// Creamos la consulta para obtener las referencias más vendidas
$query = "SELECT dp.ref, dp.color, SUM(dp.cantidad) AS cantidad
          FROM detalle_pedido dp
          JOIN pedidos p ON dp.id_pedido = p.id_pedido
          WHERE 1=1";

// Si se ha seleccionado un día, filtramos por fecha
if (!empty($dia)) {
    // Verificamos que el formato de $dia sea correcto (YYYY-MM-DD)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dia)) {
        $query .= " AND DATE(p.fecha_pedido) = ?";
        $params[] = $dia;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de fecha incorrecto. Use el formato YYYY-MM-DD.'
        ]);
        exit;
    }
} elseif (!empty($mes)) {
    // Aseguramos que el formato del mes sea correcto (YYYY-MM)
    if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
        $query .= " AND DATE_FORMAT(p.fecha_pedido, '%Y-%m') = ?";
        $params[] = $mes;
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
