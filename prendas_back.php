<?php
include('config.php');

header('Content-Type: application/json; charset=UTF-8'); // Respuestas en formato JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar si se solicita listar las prendas agotadas
        if (isset($_POST['action']) && $_POST['action'] === 'fetch_agotadas') {
            $query = "
                SELECT ref, color
                FROM inventario
                WHERE cantidad = 0
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $agotadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['agotadas' => $agotadas]);
            exit;
        }

        // Buscar cantidad por referencia
        if (isset($_POST['search_ref'])) {
            $ref = trim($_POST['search_ref']); // Limpiar el input

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

            echo json_encode(['prendas' => $prendas]);
            exit;
        }

        echo json_encode(['error' => 'Solicitud no válida.']);
        exit;
    } else {
        echo json_encode(['error' => 'Método no permitido.']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    exit;
}
?>
