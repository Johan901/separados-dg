document.getElementById("filtro-asesor").addEventListener("submit", function(event) {
    event.preventDefault();

    const asesor = document.getElementById("asesor").value;
    const fecha_inicio = document.getElementById('fecha_inicio').value;
    const fecha_fin = document.getElementById('fecha_fin').value;

    // Verificamos que tanto asesor como las fechas estén presentes
    if (asesor && fecha_inicio && fecha_fin) {
        // Hacemos una llamada AJAX a 'generar_reporte.php' pasando las fechas y el asesor
        $.ajax({
            url: 'generar_reporte.php',
            method: 'POST',
            data: { asesor: asesor, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
            success: function(response) {
                // Mostrar la respuesta (el reporte) en el contenedor con id "reporte"
                document.getElementById("reporte").innerHTML = response;
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX: ", status, error);
            }
        });
    } else {
        alert("Por favor, selecciona un asesor y las fechas.");
    }
});

