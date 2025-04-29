<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['asesor']) && isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
        // Reporte por Asesor
        $asesor = $_POST['asesor'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        try {
            // Agregar la condición para solo seleccionar pedidos con estado 'abierto'
            $query = "
                SELECT p.id_pedido, p.fecha_pedido, p.total_pedido
                FROM pedidos p
                WHERE p.asesor = :asesor 
                AND p.estado = 'abierto'  -- Filtra solo los pedidos abiertos
                AND DATE(p.fecha_pedido) BETWEEN :fecha_inicio AND :fecha_fin
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['asesor' => $asesor, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countQuery = "
                SELECT COUNT(*) as num_pedidos
                FROM pedidos p
                WHERE p.asesor = :asesor
                AND p.estado = 'abierto'  -- Filtra solo los pedidos abiertos
                AND DATE(p.fecha_pedido) BETWEEN :fecha_inicio AND :fecha_fin
            ";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute(['asesor' => $asesor, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $numPedidos = $countResult['num_pedidos'];

            if ($ventas) {
                echo "<h3>Reporte de separados abiertos para la línea $asesor desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<p>La línea $asesor ha realizado un total de $numPedidos separados abiertos en las fechas seleccionadas.</p>";
                echo "<table><thead><tr><th>ID Pedido</th><th>Fecha Pedido</th><th>Total Pedido</th></tr></thead><tbody>";

                $dataArray = [];
                foreach ($ventas as $venta) {
                    echo "<tr><td>" . $venta['id_pedido'] . "</td><td>" . $venta['fecha_pedido'] . "</td><td>" . $venta['total_pedido'] . "</td></tr>";
                    $dataArray[] = "['" . $venta['fecha_pedido'] . "', " . $venta['total_pedido'] . "]";
                }

                echo "</tbody></table>";

                $chartData = implode(',', $dataArray);

                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawChart);
            
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Fecha', 'Total Pedido'],
                            $chartData
                        ]);
            
                        var options = {
                            title: 'Separados Abiertos por Fecha',
                            chartArea: {width: '50%'},
                            hAxis: {title: 'Fecha', minValue: 0},
                            vAxis: {title: 'Total Pedido'},
                            series: {0: {color: '#800020'}}
                        };
            
                        var chart = new google.visualization.BarChart(document.getElementById('ventasChart'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='ventasChart' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron separados abiertos para este asesor y rango de fechas.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . $e->getMessage() . "</p>";
        }
    } elseif (isset($_POST['fecha_inicio_conjunto']) && isset($_POST['fecha_fin_conjunto'])) {
        // Reporte Conjunto
        $fecha_inicio = $_POST['fecha_inicio_conjunto'];
        $fecha_fin = $_POST['fecha_fin_conjunto'];

        try {
            // Agregar la condición para solo seleccionar pedidos con estado 'abierto'
            $query = "
                SELECT p.asesor, COUNT(*) as num_pedidos, SUM(p.total_pedido) as total_ventas
                FROM pedidos p
                WHERE DATE(p.fecha_pedido) BETWEEN :fecha_inicio AND :fecha_fin
                AND p.estado = 'abierto'  -- Filtra solo los pedidos abiertos
                GROUP BY p.asesor
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $ventasConjunto = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($ventasConjunto) {
                echo "<h3>Reporte conjunto de ventas abiertas desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Asesor</th><th>Número de Pedidos</th><th>Total Ventas</th></tr></thead><tbody>";

                $chartDataConjunto = [["Asesor", "Total Ventas"]];
                foreach ($ventasConjunto as $venta) {
                    echo "<tr><td>" . $venta['asesor'] . "</td><td>" . $venta['num_pedidos'] . "</td><td>" . $venta['total_ventas'] . "</td></tr>";
                    $chartDataConjunto[] = [$venta['asesor'], (int)$venta['total_ventas']];
                }

                echo "</tbody></table>";

                $chartDataConjuntoJson = json_encode($chartDataConjunto);

                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChartConjunto);
            
                    function drawChartConjunto() {
                        var data = google.visualization.arrayToDataTable($chartDataConjuntoJson);
            
                        var options = {
                            title: 'Ventas Totales Abiertas por Asesor',
                            pieHole: 0.4,
                            chartArea: {width: '80%', height: '80%'}
                        };
            
                        var chart = new google.visualization.PieChart(document.getElementById('ventasChartConjunto'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='ventasChartConjunto' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron ventas abiertas en el rango de fechas seleccionado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Por favor, completa todos los campos requeridos.</p>";
    }
}
?>
