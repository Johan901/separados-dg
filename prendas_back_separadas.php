<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['asesor']) && isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
        // Reporte por Asesor con detalles de ref y color
        $asesor = $_POST['asesor'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        try {
            $query = "
                SELECT 
                    dp.ref, 
                    dp.color, 
                    COUNT(dp.ref) AS cantidad_separada
                FROM pedidos p
                INNER JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                WHERE p.asesor = :asesor
                AND p.estado = 'abierto'  -- Filtrar solo pedidos abiertos
                AND DATE(p.fecha_pedido) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY dp.ref, dp.color
                ORDER BY cantidad_separada DESC
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['asesor' => $asesor, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultados) {
                echo "<h3>Reporte de prendas separadas por asesor: $asesor</h3>";
                echo "<table><thead><tr><th>Referencia</th><th>Color</th><th>Cantidad Separada</th></tr></thead><tbody>";

                $chartData = [["Referencia", "Cantidad Separada"]];

                foreach ($resultados as $fila) {
                    echo "<tr><td>" . $fila['ref'] . "</td><td>" . $fila['color'] . "</td><td>" . $fila['cantidad_separada'] . "</td></tr>";
                    $chartData[] = [$fila['ref'] . " - " . $fila['color'], (int)$fila['cantidad_separada']];
                }

                echo "</tbody></table>";

                $chartDataJson = json_encode($chartData);

                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawChart);
            
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable($chartDataJson);
            
                        var options = {
                            title: 'Prendas separadas por referencia y color',
                            chartArea: {width: '60%'},
                            hAxis: {title: 'Referencia - Color', minValue: 0},
                            vAxis: {title: 'Cantidad Separada'},
                            series: {0: {color: '#4CAF50'}}
                        };
            
                        var chart = new google.visualization.ColumnChart(document.getElementById('separadosChart'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='separadosChart' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron prendas separadas para este asesor en el rango de fechas seleccionado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Por favor, completa todos los campos requeridos.</p>";
    }
}
?>
