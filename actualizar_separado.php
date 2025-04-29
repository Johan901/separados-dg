<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

try {
    if (!isset($_POST['id']) || !isset($_POST['separado'])) {
        throw new Exception("Datos incompletos recibidos.");
    }

    $id_pedido = $_POST['id'];
    $separado = $_POST['separado'] == '1' ? 1 : 0;

    $stmt = $conn->prepare("UPDATE pedidos SET separado_check = :separado WHERE id_pedido = :id_pedido");
    $stmt->bindParam(':separado', $separado, PDO::PARAM_BOOL);
    $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "ERROR en ejecución de SQL.";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
