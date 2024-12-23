<?php
include('config.php');

header('Content-Type: application/json'); // Asegúrate de que la respuesta es JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar si es una búsqueda por referencia
        if (isset($_POST['search_ref'])) {
            $ref = trim($_POST['search_ref']); // Eliminar espacios en blanco innecesarios

            if (empty($ref)) {
                echo json_encode(['error' => 'La referencia no puede estar vacía.']);
                exit;
            }

            $query = "
                SELECT color, cantidad
                FROM inventario
                WHERE ref = :ref
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':ref', $ref, PDO::PARAM_STR);
            $stmt->execute();
            $prendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($agotadas) {
                echo json_encode(array('agotadas' => $agotadas));
            } else {
                echo json_encode(array('agotadas' => []));
            }

        } else {
            // Si no se proporciona 'search_ref', buscamos las prendas agotadas
            $query = "
                SELECT ref, color
                FROM inventario
                WHERE cantidad = 0
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $agotadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($agotadas)) {
                echo json_encode(['message' => 'No hay prendas agotadas en el inventario.']);
            } else {
                echo json_encode(['agotadas' => $agotadas]);
            }
        }
    } catch (PDOException $e) {
        // En caso de error con la base de datos
        echo json_encode(['error' => 'Error al obtener los datos: ' . $e->getMessage()]);
    }
}
?>
