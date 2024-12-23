<?php
include 'config.php';
$ref = $_GET['ref'];
$sql = "SELECT tipo_prenda, precio FROM inventario WHERE ref = '$ref'";
$result = $conn->query($sql);
$info = $result->fetch_assoc();
$info['colores'] = [];
$sql = "SELECT color FROM inventario WHERE ref = '$ref'";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $info['colores'][] = $row['color'];
}
echo json_encode($info);
$conn->close();
?>
