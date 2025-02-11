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
                    SUM(dp.cantidad) AS total_cantidad
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE p.asesor = :asesor 
                AND p.estado = 'abierto'
                AND dp.fecha_agregado BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY dp.ref, dp.color
                ORDER BY dp.ref, dp.color
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['asesor' => $asesor, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultados) {
                $total_prendas = 0;
            
                foreach ($resultados as $fila) {
                    $total_prendas += $fila['total_cantidad'];
                }
            
                echo "<h3>Reporte de prendas separadas por asesor: $asesor ha separado ($total_prendas) prendas</h3>";
                echo "<table><thead><tr><th>Referencia</th><th>Color</th><th>Cantidad Separada</th></tr></thead><tbody>";
            
                $chartData = [["Referencia", "Cantidad Separada"]];
            
                foreach ($resultados as $fila) {
                    echo "<tr><td>" . $fila['ref'] . "</td><td>" . $fila['color'] . "</td><td>" . $fila['total_cantidad'] . "</td></tr>";
                    $chartData[] = [$fila['ref'] . " - " . $fila['color'], (int)$fila['total_cantidad']];
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
                            series: {0: {color: '#e91d29'}}
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
