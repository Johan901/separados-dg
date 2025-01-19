<?php
include('config.php');

if (isset($_POST['action']) && $_POST['action'] == 'fetch_references') {
    $id_pedido = intval($_POST['id_pedido']); // Sanitización de entrada

    $sql = "SELECT ref, color FROM detalle_pedido WHERE id_pedido = $1";
    $result = pg_query_params($conn, $sql, [$id_pedido]);

    if ($result) {
        $references = [];
        while ($row = pg_fetch_assoc($result)) {
            $references[] = $row;
        }
        echo json_encode($references);
    } else {
        echo json_encode(['error' => 'No se encontraron referencias.']);
    }
}
?>
