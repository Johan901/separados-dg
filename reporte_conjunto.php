<?php
include('config.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_inicio_conjunto']) && isset($_POST['fecha_fin_conjunto'])) {
        $fecha_inicio = $_POST['fecha_inicio_conjunto'];
        $fecha_fin = $_POST['fecha_fin_conjunto'];

        try {
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

            $suma_total_ventas = 0;

            if ($ventasConjunto) {
                echo "<h3>Reporte conjunto de pedidos cerrados desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Asesor</th><th>Número de Pedidos</th><th>Total Ventas</th></tr></thead><tbody>";

                $chartDataConjunto = [["Asesor", "Número de Pedidos", ["role" => "style"]]];
                $colors = ['#e91d29', '#e91d29', '#e91d29', '#e91d29', '#e91d29'];
                $colorIndex = 0;

                foreach ($ventasConjunto as $venta) {
                    $color = $colors[$colorIndex % count($colors)];
                    $totalVentasFormatted = "$" . number_format($venta['total_ventas'], 0, ',', '.');
                    $suma_total_ventas += $venta['total_ventas'];

                    echo "<tr>
                            <td>" . htmlspecialchars($venta['asesor']) . "</td>
                            <td>" . htmlspecialchars($venta['num_pedidos']) . "</td>
                            <td>" . htmlspecialchars($totalVentasFormatted) . "</td>
                          </tr>";
                    $chartDataConjunto[] = [$venta['asesor'], (int)$venta['num_pedidos'], $color];
                    $colorIndex++;
                }

                echo "</tbody></table>";

                $chartDataConjuntoJson = json_encode($chartDataConjunto);
                $suma_total_ventas_formateado = "$" . number_format($suma_total_ventas, 0, ',', '.');

                // Mensaje animado de alerta guapa
                echo "
                <style>
                    @keyframes parpadeo {
                        0% { opacity: 1; }
                        50% { opacity: 0.5; }
                        100% { opacity: 1; }
                    }
                    #mensajeVentas {
                        font-size: 22px;
                        font-weight: bold;
                        color: #e91d29;
                        text-align: center;
                        margin: 20px 0;
                        animation: parpadeo 1.5s infinite ease-in-out;
                        padding: 10px;
                        border-radius: 10px;
                        background: rgba(233, 29, 41, 0.1);
                    }
                </style>

                <div id='mensajeVentas'>
                    🚀 ¡Por ahora vamos vendiendo <span id='totalVentas'>$suma_total_ventas_formateado</span> COP! 🎉
                </div>
                ";

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
