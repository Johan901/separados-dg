<?php
include('config.php'); // Asegúrate de incluir tu archivo de configuración para la conexión a la base de datos

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si estamos buscando una referencia
    if (isset($_POST['search_ref'])) {
        $ref = $_POST['search_ref'];

        try {
            // Consultar la cantidad por referencia y color
            $query = "
                SELECT color, cantidad
                FROM inventario
                WHERE ref = :ref
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ref', $ref, PDO::PARAM_STR);
            $stmt->execute();
            $prendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retornar los resultados en formato JSON
            echo json_encode(['prendas' => $prendas]);

        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    }
    // Verificar si estamos consultando prendas agotadas
    else {
        try {
            // Consultar todas las prendas con cantidad igual a 0 (agotadas)
            $query = "
                SELECT ref, color
                FROM inventario
                WHERE cantidad = 0
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $agotadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retornar los resultados en formato JSON
            echo json_encode(['agotadas' => $agotadas]);

        } catch (PDOException $e) {
            echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
        }
    }
}
?>
