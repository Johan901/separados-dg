<?php
include('config.php');
header('Content-Type: application/json');

if (isset($_GET['referencia'])) {
    $referencia = $_GET['referencia'];

    // Prepara y ejecuta la consulta para buscar tipo_prenda, precio_al_detal y precio_por_mayor
    $stmt = $conn->prepare("SELECT tipo_prenda, precio_al_detal, precio_por_mayor FROM INVENTARIO WHERE ref = :referencia LIMIT 1");
    $stmt->bindParam(':referencia', $referencia);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        // Ahora busca todos los colores para esa referencia
        $stmtColores = $conn->prepare("SELECT color FROM INVENTARIO WHERE ref = :referencia");
        $stmtColores->bindParam(':referencia', $referencia);
        $stmtColores->execute();

        $colores = $stmtColores->fetchAll(PDO::FETCH_COLUMN); // Obtiene solo los colores

        // Agrega los colores al resultado
        $resultado['colores'] = $colores;

        echo json_encode($resultado);
    } else {
        echo json_encode(["error" => "Referencia no encontrada."]);
    }
} else {
    echo json_encode(["error" => "Referencia no proporcionada."]);
}
?>
