<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';  // Asegúrate de que la conexión a la base de datos esté bien configurada

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_deactivated') {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        echo json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]);
        exit;
    }

    $query = "SELECT asesor, cliente_cedula, envio, estado, fecha_limite, fecha_pedido, id_pedido, medio_conocimiento, observaciones, pedido_separado, total_pedido 
              FROM pedidos 
              WHERE estado = 'Desarmado'";

    $result = $conn->query($query);

    if ($result) {
        $pedidos = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos[] = $row;
        }
        echo json_encode(['pedidos' => $pedidos]);
    } else {
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['error' => 'Solicitud inválida']);
}
?>
