<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos enviados por el formulario
    $asesor = $_POST['asesor'];
    $fechaInicio = $_POST['fecha_inicio'];
    $fechaFin = $_POST['fecha_fin'];

    // Escapar los valores para evitar problemas de inyección
    $asesor = escapeshellarg($asesor);
    $fechaInicio = escapeshellarg($fechaInicio);
    $fechaFin = escapeshellarg($fechaFin);

    // Ejecutar el script Python pasando los parámetros
    $comando = "python3 C:/xampp/htdocs/separados-dg/reporte.py $asesor $fechaInicio $fechaFin";
    $resultado = shell_exec($comando);

    // Prepara la respuesta en formato JSON
    $response = [
        'status' => 'success',
        'message' => 'Reportes Generados Exitosamente',
        'reporte_individual' => "/separados-dg/reporte_individual_$asesor.png",
        'reporte_grupal' => "/separados-dg/reporte_grupal.png",
        'csv_individual' => "/separados-dg/reporte_individual_$asesor.csv",
        'csv_grupal' => "/separados-dg/reporte_grupal.csv"
    ];

    // Devolver la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
