<?php
// Asegúrate de que esta línea incluye la conexión correcta
include 'config.php'; // Incluye tu conexión a la base de datos

$referencia = $_GET['ref'];
$color = $_GET['color'];

// Consulta para obtener la cantidad disponible
$query = "SELECT cantidad FROM inventario WHERE ref = :referencia AND color = :color";
$stmt = $conn->prepare($query);

// Enlazar parámetros
$stmt->bindParam(':referencia', $referencia);
$stmt->bindParam(':color', $color);

$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo json_encode(['cantidadDisponible' => (int)$result['cantidad']]);
} else {
    echo json_encode(['error' => 'Referencia no encontrada.']);
}

$stmt = null; // Cerrar el statement
$conn = null; // Cerrar la conexión
?>
