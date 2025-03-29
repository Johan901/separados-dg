<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require 'config.php';

$conn->exec("LISTEN new_observation;"); // Escuchar el canal

while (true) {
    $result = $conn->query("SELECT 1"); // Mantiene la conexión activa

    $notification = $conn->pgsqlGetNotify(PDO::FETCH_ASSOC, 1000); // Espera notificación (1s)
    
    if ($notification) {
        file_put_contents('log.txt', json_encode($notification) . PHP_EOL, FILE_APPEND); // 🛠 Guarda logs
        
        if (!empty($notification['payload'])) {
            echo "data: " . $notification['payload'] . "\n\n";
            ob_flush();
            flush();
        }
    }

    sleep(1); // Evita consumo excesivo de CPU
}
?>
