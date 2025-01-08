<?php
// Aquí se realiza la conexión a la base de datos
include('config.php');  // Asegúrate de tener tu conexión a la base de datos

$cedula = $_GET['cedula'];

// Consulta para obtener el último pedido abierto
$query = "SELECT asesor, medio_conocimiento, envio FROM pedidos WHERE cedula_cliente = '$cedula' AND estado = 'abierto' ORDER BY fecha_pedido DESC LIMIT 1";
$result = mysqli_query($conexion, $query);

if ($result) {
    $pedido = mysqli_fetch_assoc($result);
    if ($pedido) {
        echo json_encode($pedido);  // Retorna los datos del último pedido abierto
    } else {
        echo json_encode(['error' => 'No se encontraron pedidos abiertos para este cliente']);
    }
} else {
    echo json_encode(['error' => 'Error al realizar la consulta']);
}

mysqli_close($conexion);
?>
