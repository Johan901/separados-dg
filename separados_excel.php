<?php
include('config.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Separados_Act.xls");

$query = "SELECT dp.id_pedido, dp.ref, dp.color, SUM(dp.cantidad) AS total_cantidad 
          FROM detalle_pedido dp 
          JOIN pedidos p ON dp.id_pedido = p.id_pedido 
          WHERE p.estado = 'abierto' 
          GROUP BY dp.id_pedido, dp.ref, dp.color 
          ORDER BY total_cantidad DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "ID Pedido\tReferencia\tColor\tCantidad Total\n";
foreach ($result as $row) {
    echo "{$row['id_pedido']}\t{$row['ref']}\t{$row['color']}\t{$row['total_cantidad']}\n";
}
?>
