<?php
include('config.php');

header('Content-Type: application/json; charset=UTF-8'); // Respuestas en formato JSON

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar si es una recuperación de prendas
        if (isset($_POST['recover_ref'])) {
            $ref = trim($_POST['recover_ref']); // Limpiar el input
            $cantidad = intval($_POST['recover_qty']); // Convertir a número entero

            if (empty($ref) || $cantidad <= 0) {
                echo json_encode(['error' => 'Referencia o cantidad inválida.']);
                exit;
            }

            $query = "
                UPDATE inventario
                SET cantidad = :cantidad
                WHERE ref = :ref
            ";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':ref', $ref, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode(['success' => 'Prenda recuperada exitosamente.']);
            exit;
        }

        // Verificar si es una búsqueda por referencia
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

        // Si no se proporciona 'search_ref', buscamos las prendas agotadas
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
    } else {
        echo json_encode(['error' => 'Método no permitido.']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    exit;
}
?>
