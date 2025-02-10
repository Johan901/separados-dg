<?php
include('config.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_inicio_conjunto']) && isset($_POST['fecha_fin_conjunto'])) {
        // Variables del formulario
        $fecha_inicio = $_POST['fecha_inicio_conjunto'];
        $fecha_fin = $_POST['fecha_fin_conjunto'];

        try {
            // Consulta para obtener el reporte conjunto filtrado por estado "cerrado"
            $query = "
                SELECT p.asesor, COUNT(*) as num_pedidos, SUM(p.total_pedido) as total_ventas
                FROM pedidos p
                WHERE DATE(p.fecha_limite) BETWEEN :fecha_inicio AND :fecha_fin
                AND p.estado = 'cerrado'
                GROUP BY p.asesor
            ";
            $stmt = $conn->prepare($query);
            $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $ventasConjunto = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($ventasConjunto) {
                echo "<h3>Reporte conjunto de pedidos cerrados desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Asesor</th><th>Número de Pedidos</th><th>Total Ventas</th></tr></thead><tbody>";

                // Preparar datos para el gráfico
                $chartDataConjunto = [["Asesor", "Número de Pedidos", ["role" => "style"]]];
                $colors = ['#1e88e5', '#1565c0', '#0d47a1', '#2196f3', '#42a5f5']; // Colores azulados
                $colorIndex = 0;

                foreach ($ventasConjunto as $venta) {
                    $color = $colors[$colorIndex % count($colors)];
                    echo "<tr><td>" . htmlspecialchars($venta['asesor']) . "</td><td>" . htmlspecialchars($venta['num_pedidos']) . "</td><td>" . htmlspecialchars($venta['total_ventas']) . "</td></tr>";
                    $chartDataConjunto[] = [$venta['asesor'], (int)$venta['num_pedidos'], $color];
                    $colorIndex++;
                }

                echo "</tbody></table>";

                // Convertir datos del gráfico a formato JSON
                $chartDataConjuntoJson = json_encode($chartDataConjunto);

                // Script del gráfico 
                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawChartConjunto);
                    
                    function drawChartConjunto() {
                        var data = google.visualization.arrayToDataTable($chartDataConjuntoJson);

                        var options = {
                            title: 'Pedidos Cerrados por Asesor',
                            chartArea: {width: '50%'},
                            hAxis: {title: 'Número de Pedidos'},
                            vAxis: {title: 'Asesor'},
                            series: {0: {color: '#e91d29'}}
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('ventasChartConjunto'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='ventasChartConjunto' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron pedidos cerrados en el rango de fechas seleccionado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Por favor, completa todos los campos requeridos.</p>";
    }
}
?>
