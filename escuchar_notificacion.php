<?php
include('config.php');

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$channel = 'new_observation';

$conn->exec("LISTEN $channel;");

echo "event: connected\ndata: Conectado al canal '$channel'\n\n";
ob_flush();
flush();

while (true) {
    $result = $conn->pgsqlGetNotify(PDO::FETCH_ASSOC, 10000);
    if ($result) {
        echo "event: message\n";
        echo "data: " . json_encode($result['payload']) . "\n\n";
        ob_flush();
        flush();
    }
    usleep(100000); // Pequeña pausa para evitar alta carga en el servidor
}
?>
