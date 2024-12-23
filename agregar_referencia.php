<?php
include 'config.php'; // Incluir la conexión

$response = ""; // Variable para manejar la respuesta

// Verifica si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

// Si se envía el formulario, procesa e inserta en la base de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $ref = $_POST['ref']; // Referencia
    $tipo_prenda = $_POST['tipo_prenda']; // Tipo de prenda
    $color = $_POST['color']; // Color
    $cantidad = $_POST['cantidad']; // Cantidad
    $precio_al_detal = $_POST['precio_al_detal']; // Precio al detal
    $precio_por_mayor = $_POST['precio_por_mayor']; // Precio por mayor
    
    try {
        // Verificar si la referencia ya existe
        $checkQuery = "SELECT COUNT(*) FROM inventario WHERE ref = :ref";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':ref', $ref);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // La referencia ya existe, devolver error
            $response = "duplicate";
        } else {
            // Insertar nueva referencia si no existe
            $query = "INSERT INTO inventario (ref, tipo_prenda, color, cantidad, precio_al_detal, precio_por_mayor) 
                      VALUES (:ref, :tipo_prenda, :color, :cantidad, :precio_al_detal, :precio_por_mayor)";
            
            $stmt = $conn->prepare($query);

            // Vincular los parámetros
            $stmt->bindParam(':ref', $ref);
            $stmt->bindParam(':tipo_prenda', $tipo_prenda);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':precio_al_detal', $precio_al_detal);
            $stmt->bindParam(':precio_por_mayor', $precio_por_mayor);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $response = "success";
            } else {
                $response = "error";
            }
        }
    } catch (PDOException $e) {
        $response = "error: " . addslashes($e->getMessage());
    }
}
?>