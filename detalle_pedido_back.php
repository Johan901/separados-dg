<?php
include('config.php');

if (isset($_POST['action']) && $_POST['action'] == 'fetch_references') {
    $id_pedido = $_POST['id_pedido'];
    $sql = "SELECT ref, id_detalle FROM detalle_pedido WHERE id_pedido = $id_pedido";
    $result = pg_query($conn, $sql);

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