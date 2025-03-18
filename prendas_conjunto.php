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
                    SUM(dp.cantidad) AS total_cantidad
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE DATE(dp.fecha_agregado) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY p.asesor
            ";

            $stmt = $conn->prepare($query);
            $stmt->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($resultados) {
                echo "<h3>Prendas separadas desde $fecha_inicio hasta $fecha_fin</h3>";
                echo "<table><thead><tr><th>Asesor</th><th>Número de Prendas Separadas</th></tr></thead><tbody>";

                $chartData = [["Asesor", "Número de Prendas"]];
                $suma_total_prendas = 0;

                foreach ($resultados as $fila) {
                    $suma_total_prendas += $fila['total_cantidad'];
                    echo "<tr>
                            <td>" . htmlspecialchars($fila['asesor']) . "</td>
                            <td>" . htmlspecialchars($fila['total_cantidad']) . "</td>
                          </tr>";
                    $chartData[] = [$fila['asesor'], (int)$fila['total_cantidad']];
                }

                echo "</tbody></table>";

                $chartDataJson = json_encode($chartData);

                // Mensaje animado bonito
                echo "
                <style>
                    @keyframes fade {
                        0% { opacity: 1; }
                        50% { opacity: 0.5; }
                        100% { opacity: 1; }
                    }
                    #mensajePrendas {
                        font-size: 22px;
                        font-weight: bold;
                        color: #e91d29;
                        text-align: center;
                        margin: 20px 0;
                        animation: fade 1.5s infinite ease-in-out;
                        padding: 10px;
                        border-radius: 10px;
                        background: rgba(233, 29, 41, 0.1);
                    }
                </style>

                <div id='mensajePrendas'>
                    ✨ Por ahora vamos por <span id='totalPrendas'>$suma_total_prendas</span> prendas separadas en total ✨
                </div>
                ";

                // Script del gráfico
                echo "
                <script type='text/javascript'>
                    google.charts.load('current', {'packages':['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawChart);
                    
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable($chartDataJson);

                        var options = {
                            title: 'Prendas Separadas por Asesor',
                            chartArea: {width: '50%'},
                            hAxis: {title: 'Número de Prendas'},
                            vAxis: {title: 'Asesor'}
                        };

                        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
                        chart.draw(data, options);
                    }
                </script>
                <div id='chart_div' style='width: 900px; height: 500px;'></div>
                ";
            } else {
                echo "<p>No se encontraron prendas separadas en el rango de fechas seleccionado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Error al obtener los datos: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Por favor, completa todos los campos requeridos.</p>";
    }
}
?>
