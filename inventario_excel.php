<?php
include('config.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Inventario_Dispo.xls");

// Nueva consulta con las columnas correctas
$query = "SELECT ref, tipo_prenda, color, cantidad, precio_al_detal, precio_por_mayor, fecha_creacion 
          FROM inventario 
          WHERE cantidad > 0 
          ORDER BY cantidad DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Imprimir encabezados de columnas
echo "Referencia\tTipo de Prenda\tColor\tCantidad\tPrecio al Detal\tPrecio por Mayor\tFecha de Creación\n";

// Imprimir datos
foreach ($result as $row) {
    echo "{$row['ref']}\t{$row['tipo_prenda']}\t{$row['color']}\t{$row['cantidad']}\t{$row['precio_al_detal']}\t{$row['precio_por_mayor']}\t{$row['fecha_creacion']}\n";
}
?>
