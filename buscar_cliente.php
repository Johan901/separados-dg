<?php
include('config.php');
header('Content-Type: application/json'); // Asegúrate de establecer el tipo de contenido

// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}



if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE cedula = :cedula");
    $stmt->bindParam(':cedula', $cedula);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        // Devuelve el resultado como JSON
        echo json_encode($resultado);
    } else {
        // Si no se encuentra el cliente
        echo json_encode(["error" => "Cliente no encontrado."]);
    }
} else {
    // Si la cédula no fue proporcionada
    echo json_encode(["error" => "Cédula no proporcionada."]);
}
?>
