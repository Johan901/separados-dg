<?php
include('config.php');
header('Content-Type: application/json');


if (isset($_POST['action']) && $_POST['action'] == 'fetch_deactivated') {
    $sql = "SELECT * FROM pedidos WHERE estado = 'eliminado'";
    $result = pg_query($conn, $sql);

    if ($result) {
        $pedidos = [];
        while ($row = pg_fetch_assoc($result)) {
            $pedidos[] = $row;
        }
        echo json_encode(['pedidos' => $pedidos]);
    } else {
        echo json_encode(['error' => 'No se pudieron obtener los pedidos.', 'pg_error' => pg_last_error($conn)]);
    }
}
?>
