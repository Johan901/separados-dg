<?php
// Incluimos la conexión PDO
include('config.php');  // Asegúrate de que tienes el archivo de conexión configurado correctamente

$cedula = $_GET['cedula'];

try {
    // Consulta para obtener el último pedido abierto
    $query = "SELECT asesor, medio_conocimiento, envio 
              FROM pedidos 
              WHERE cedula_cliente = :cedula AND estado = 'abierto' 
              ORDER BY fecha_pedido DESC LIMIT 1";

    // Preparar la consulta
    $stmt = $conexion->prepare($query);

    // Enlazar los parámetros
    $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pedido) {
        // Retornar los datos del último pedido abierto en formato JSON
        echo json_encode($pedido);
    } else {
        echo json_encode(['error' => 'No se encontraron pedidos abiertos para este cliente']);
    }
} catch (PDOException $e) {
    // Capturar errores y mostrar el mensaje
    echo json_encode(['error' => 'Error al realizar la consulta: ' . $e->getMessage()]);
}
?>
