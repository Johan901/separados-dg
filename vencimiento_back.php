<?php
include('config.php'); // Asegúrate de incluir tu archivo de conexión

// Calculamos la fecha límite para la próxima semana
$fecha_actual = date('Y-m-d'); // Fecha actual
$fecha_limite = date('Y-m-d', strtotime('+7 days', strtotime($fecha_actual)));

// Preparamos la consulta SQL
$query = "
    SELECT p.id_pedido, p.fecha_pedido, p.total_pedido, p.asesor, p.envio, p.fecha_limite, p.estado, p.medio_conocimiento, p.pedido_separado, p.observaciones, c.nombre AS cliente_nombre, c.cedula AS cliente_cedula
    FROM pedidos p
    INNER JOIN clientes c ON p.cliente_cedula = c.cedula
    WHERE p.fecha_limite BETWEEN :fecha_actual AND :fecha_limite
    AND p.estado != 'completado'
";

// Preparamos la consulta con PDO
$stmt = $conn->prepare($query);

// Enlazamos los parámetros
$stmt->bindParam(':fecha_actual', $fecha_actual, PDO::PARAM_STR);
$stmt->bindParam(':fecha_limite', $fecha_limite, PDO::PARAM_STR);

// Ejecutamos la consulta
$stmt->execute();

// Obtenemos los resultados
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($pedidos) {
    // Formateamos el total_pedido como peso colombiano
    foreach ($pedidos as &$pedido) {
        $pedido['total_pedido'] = '$' . number_format($pedido['total_pedido'], 0, ',', '.');
    }

    // Devolver los resultados en formato JSON
    echo json_encode(['pedidos' => $pedidos]);
} else {
    // Si no hay resultados
    echo json_encode(['error' => 'No se encontraron pedidos próximos a vencer.']);
}
?>
