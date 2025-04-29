<?php 
require_once 'config.php'; // Conexión usando $conn y PDO

$id = $_POST['id'];
$separado = $_POST['separado'] == '1' ? 1 : 0;

// Asegúrate de validar que $id no sea vacío o inválido si quieres más seguridad
$stmt = $conn->prepare("UPDATE pedidos SET separado_check = :separado WHERE id = :id");
$stmt->bindParam(':separado', $separado, PDO::PARAM_BOOL);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "ERROR";
}
?>
