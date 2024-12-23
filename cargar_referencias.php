<?php
include 'config.php';
$sql = "SELECT ref FROM inventario";
$result = $conn->query($sql);
$refs = [];
while ($row = $result->fetch_assoc()) {
    $refs[] = $row;
}
echo json_encode($refs);
$conn->close();
?>