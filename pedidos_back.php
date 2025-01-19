<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';  // Asegúrate de que config.php configura correctamente la conexión con PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_deactivated') {
    try {
        // Conectar con la base de datos usando PDO
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT asesor, cliente_cedula, envio, estado, fecha_limite, fecha_pedido, id_pedido, medio_conocimiento, 
                         observaciones, pedido_separado, total_pedido 
                  FROM pedidos 
                  WHERE estado = 'eliminado'";

        $stmt = $conn->prepare($query);
        $stmt->execute();

        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['pedidos' => $pedidos]);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Solicitud inválida']);
}
?>
