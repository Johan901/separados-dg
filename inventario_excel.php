<?php
include('config.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Inventario_Dispo.xls");

$query = "SELECT * FROM inventario WHERE cantidad > 0 ORDER BY cantidad DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "ID\tProducto\tCantidad\tPrecio\n";
foreach ($result as $row) {
    echo "{$row['id']}\t{$row['producto']}\t{$row['cantidad']}\t{$row['precio']}\n";
}
?>
