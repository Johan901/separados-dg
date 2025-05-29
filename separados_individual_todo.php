<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['asesor']) && isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
        $asesor = $_POST['asesor'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        try {
            $query = "
                SELECT DISTINCT dp.id_pedido, p.fecha_pedido, p.total_pedido
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE p.asesor = :asesor
                AND DATE(dp.fecha_agregado) BETWEEN :fecha_inicio AND :fecha_fin
                ORDER BY dp.id_pedido DESC
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                'asesor' => $asesor,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = count($resultados);

            if ($resultados) {
                echo "<h3>Pedidos donde el asesor <strong>$asesor</strong> ingresó prendas entre $fecha_inicio y $fecha_fin</h3>";
                echo "<p>Total de OP diferentes: <strong>$total</strong></p>";
                echo "<table><thead><tr><th>ID Pedido</th><th>Fecha Pedido</th><th>Total Pedido</th></tr></thead><tbody>";

                foreach ($resultados as $fila) {
                    echo "<tr><td>{$fila['id_pedido']}</td><td>{$fila['fecha_pedido']}</td><td>{$fila['total_pedido']}</td></tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No se encontraron OP ingresadas por el asesor en ese rango de fechas.</p>";
            }

        } catch (PDOException $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Por favor completa todos los campos del formulario.</p>";
    }
}
?>
