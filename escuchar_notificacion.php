<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require 'config.php'; // Conexión a la BD

$conn->exec("LISTEN new_observation;"); // Escuchar el canal 'new_observation'

while (true) {
    $result = $conn->query("SELECT 1"); // Evita desconexiones

    if ($conn->pgsqlGetNotify(PDO::FETCH_ASSOC, 1000)) { // Esperar notificación
        $notification = $conn->pgsqlGetNotify(PDO::FETCH_ASSOC);
        if ($notification) {
            echo "data: " . $notification['payload'] . "\n\n";
            ob_flush();
            flush();
        }
    }
    sleep(1); // Evita consumo excesivo de CPU
}
?>
