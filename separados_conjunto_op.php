<?php
include('config.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_inicio_conjunto']) && isset($_POST['fecha_fin_conjunto'])) {
        $fecha_inicio = $_POST['fecha_inicio_conjunto'];
        $fecha_fin = $_POST['fecha_fin_conjunto'];

        try {
            $query = "
                SELECT 
                    p.asesor, 
                    COUNT(DISTINCT dp.id_pedido) AS total_op
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE DATE(dp.fecha_agregado) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY p.asesor
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultados) {
                echo "<h3>Reporte conjunto de OP separadas desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Línea</th><th>Pedidos (OP) separados</th></tr></thead><tbody>";

                $chartData = [["Línea", "OP Separadas"]];
                foreach ($resultados as $fila) {
                    echo "<tr><td>" . htmlspecialchars($fila['asesor']) . "</td><td>" . htmlspecialchars($fila['total_op']) . "</td></tr>";
                    $chartData[] = [$fila['asesor'], (int)$fila['total_op']];
                }

                echo "</tbody></table>";

                $chartDataJson = json_encode($chartData);

                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChartOP);

                    function drawChartOP() {
                        var data = google.visualization.arrayToDataTable($chartDataJson);

                        var options = {
                            title: 'Pedidos (OP) separados por Línea',
                            chartArea: {width: '70%', height: '70%'},
                            hAxis: {title: 'OP separadas'},
                            vAxis: {title: 'Línea'},
                            legend: {position: 'none'}
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('ventasChartConjunto'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='ventasChartConjunto' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron OP separadas en el rango de fechas seleccionado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Por favor, completa todos los campos requeridos.</p>";
    }
} else {
    echo "<p>Acceso no autorizado.</p>";
}
?>
