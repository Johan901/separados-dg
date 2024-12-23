<?php
$uri = getenv('DATABASE_URL');  // Obtiene la URL de la base de datos desde las variables de entorno

// Extraer los datos de la URI
$uri = parse_url($uri);
$host = $uri['host'];
$dbname = ltrim($uri['path'], '/');
$username = $uri['user'];
$password = $uri['pass'];
$port = 5432;  // Puerto, generalmente 5432 para PostgreSQL

header('Content-Type: application/json'); // Asegúrate de que la salida sea JSON

try {
    // Conexión a la base de datos usando PDO
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Devuelve una respuesta en formato JSON
    echo json_encode(["status" => "success", "message" => "Conexión exitosa"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
