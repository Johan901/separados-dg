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

            // Variable para calcular la suma total de ventas
            $suma_total_ventas = 0;

            if ($ventasConjunto) {
                echo "<h3>Reporte conjunto de pedidos cerrados desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Asesor</th><th>Número de Pedidos</th><th>Total Ventas</th></tr></thead><tbody>";

                // Preparar datos para el gráfico
                $chartDataConjunto = [["Asesor", "Número de Pedidos", ["role" => "style"]]];
                $colors = ['#e91d29', '#e91d29', '#e91d29', '#e91d29', '#e91d29']; // Colores rojo oscuro
                $colorIndex = 0;

                foreach ($ventasConjunto as $venta) {
                    $color = $colors[$colorIndex % count($colors)];
                    $totalVentasFormatted = "$" . number_format($venta['total_ventas'], 0, ',', '.'); // Formato COP

                    // Sumar todas las ventas
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

                // Formatear total de ventas
                $suma_total_ventas_formateado = "$" . number_format($suma_total_ventas, 0, ',', '.');

                // Mostrar el mensaje animado con JavaScript
                echo "
                <div id='mensajeVentas' style='font-size: 20px; font-weight: bold; color: #e91d29; opacity: 0; transition: opacity 1s ease-in-out;'>
                    Por ahora vamos vendiendo <span id='totalVentas'>$suma_total_ventas_formateado</span> COP 🎉
                </div>

                <script>
                    setTimeout(function() {
                        document.getElementById('mensajeVentas').style.opacity = 1;
                    }, 500);
                </script>
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
