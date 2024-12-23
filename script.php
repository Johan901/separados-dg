<?php
// Verifica si el usuario ha iniciado sesión
session_start(); // Asegúrate de iniciar la sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}
?>

<?php
include 'config.php'; // Incluye el archivo de configuración para la conexión a la BD
session_start();

// Verifica si el medio de conocimiento está presente en la URL
if (isset($_GET['medio_conocimiento'])) {
    $medio_conocimiento = $_GET['medio_conocimiento'];

    // Puedes realizar la inserción a la base de datos aquí
    // ...

    // Mensaje de éxito
    echo json_encode(['success' => true, 'medio_conocimiento' => $medio_conocimiento]);
} else {
    // Mensaje de error
    echo json_encode(['success' => false, 'error' => 'El medio de conocimiento no se ha recibido']);
}
?>