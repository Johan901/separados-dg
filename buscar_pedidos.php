<?php
// Conexión a la base de datos
include 'config.php';

// Obtener la cédula desde el parámetro GET
$cedula = $_GET['cedula'];
if (empty($cedula)) {
    echo json_encode(['error' => "Cédula no proporcionada"]);
    exit;
}

try {
    // Consulta preparada para buscar los pedidos según la cédula del cliente
    $query = $conn->prepare("
        SELECT pedidos.id_pedido, pedidos.fecha_pedido, pedidos.fecha_limite, pedidos.estado 
        FROM pedidos 
        WHERE pedidos.cliente_cedula = :cedula
    ");
    
    // Asignar el valor de la cédula
    $query->bindValue(':cedula', $cedula, PDO::PARAM_STR);

    // Ejecutar la consulta
    $query->execute();

    // Obtener los resultados
    $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si no se encontraron pedidos
    if (empty($resultados)) {
        echo json_encode(['message' => "No se encontraron pedidos para la cédula especificada."]);
    } else {
        echo json_encode($resultados);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => "Error al buscar los pedidos: " . $e->getMessage()]);
}
?>
