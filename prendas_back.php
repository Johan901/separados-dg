<?php
include('config.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Indicar que se devolverá JSON

    ob_start(); // Iniciar un buffer de salida

    if (isset($_POST['search_ref'])) {
        $ref = $_POST['search_ref'];
        try {
            $query = "
                SELECT color, cantidad
                FROM inventario
                WHERE ref = :ref
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ref', $ref, PDO::PARAM_STR);
            $stmt->execute();
            $prendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_clean();
            echo json_encode(['prendas' => $prendas]);

        } catch (PDOException $e) {
            ob_clean();
            echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    } else {
        try {
            $query = "
                SELECT ref, color
                FROM inventario
                WHERE cantidad = 0
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $agotadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_clean();
            echo json_encode(['agotadas' => $agotadas]);

        } catch (PDOException $e) {
            ob_clean();
            echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    }
}
?>
