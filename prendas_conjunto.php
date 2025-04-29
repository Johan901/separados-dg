<?php
include('config.php'); // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha_inicio_conjunto']) && isset($_POST['fecha_fin_conjunto'])) {
        $fecha_inicio = $_POST['fecha_inicio_conjunto'];
        $fecha_fin = $_POST['fecha_fin_conjunto'];

        try {
            // Obtener cantidad de prendas separadas por asesor
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

            // Obtener cantidad de prendas separadas (estado "abierto")
            $query_separadas = "
                SELECT SUM(dp.cantidad) AS total_separadas
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE p.estado = 'abierto' AND DATE(dp.fecha_agregado) BETWEEN :fecha_inicio AND :fecha_fin
            ";
            $stmt_separadas = $conn->prepare($query_separadas);
            $stmt_separadas->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $separadas = $stmt_separadas->fetch(PDO::FETCH_ASSOC)['total_separadas'] ?? 0;

            // Obtener cantidad de prendas facturadas (estado "cerrado")
            $query_facturadas = "
                SELECT SUM(dp.cantidad) AS total_facturadas
                FROM detalle_pedido dp
                INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
                WHERE p.estado = 'cerrado' AND DATE(dp.fecha_agregado) BETWEEN :fecha_inicio AND :fecha_fin
            ";
            $stmt_facturadas = $conn->prepare($query_facturadas);
            $stmt_facturadas->execute(['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
            $facturadas = $stmt_facturadas->fetch(PDO::FETCH_ASSOC)['total_facturadas'] ?? 0;

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

                // Estilos y mensajes animados
                echo "
                <style>
                    @keyframes fade {
                        0% { opacity: 1; }
                        50% { opacity: 0.5; }
                        100% { opacity: 1; }
                    }
                    .mensajeAnimado {
                        font-size: 22px;
                        font-weight: bold;
                        color: #800020;
                        text-align: center;
                        margin: 20px 0;
                        animation: fade 1.5s infinite ease-in-out;
                        padding: 10px;
                        border-radius: 10px;
                        background: rgba(233, 29, 41, 0.1);
                    }
                    .contenedorMensajes {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 10px;
                    }
                    .mensajeLateral {
                        font-size: 18px;
                        font-weight: bold;
                        color: #1e88e5;
                        padding: 10px;
                        border-radius: 10px;
                        background: rgba(30, 136, 229, 0.1);
                        animation: fade 2s infinite ease-in-out;
                    }
                </style>

                <div class='mensajeAnimado'>
                    ✨ Por ahora vamos por <span id='totalPrendas'>$suma_total_prendas</span> prendas separadas en total ✨
                </div>

                <div class='contenedorMensajes'>
                    <div class='mensajeLateral'>🛒 $separadas prendas fueron separadas</div>
                    <div class='mensajeLateral'>💰 $facturadas prendas fueron facturadas</div>
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
