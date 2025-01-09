// Función para buscar la referencia y actualizar los datos de producto
function buscarReferencia() {
    const referencia = document.getElementById('referencia-busqueda').value;

    if (referencia.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Por favor introduce una referencia',
            confirmButtonText: 'Aceptar',
        });
        return;
    }

    $.ajax({
        url: 'buscar_referencias.php',
        type: 'GET',
        data: { referencia: referencia },
        success: function(data) {
            data = typeof data === 'string' ? JSON.parse(data) : data;

            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Referencia no encontrada',
                    confirmButtonText: 'Aceptar',
                });
            } else {
                document.getElementById('tipo-prenda').value = data.tipo_prenda;

                // Guardar los precios en variables globales
                precioMayor = parseFloat(data.precio_por_mayor) || 0;
                precioDetal = parseFloat(data.precio_al_detal) || 0;

                // Actualizar el dropdown de color
                const colorSelect = document.getElementById('color');
                colorSelect.innerHTML = '<option>Seleccione un color</option>';
                data.colores.forEach(color => {
                    const option = document.createElement('option');
                    option.value = color;
                    option.textContent = color;
                    colorSelect.appendChild(option);
                });

                // Alerta de éxito cuando se encuentra la referencia
                Swal.fire({
                    icon: 'success',
                    title: 'Referencia encontrada satisfactoriamente',
                    confirmButtonText: 'Aceptar',
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'Ocurrió un error al intentar buscar la referencia. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar',
            });
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    // Aquí puedes agregar otras funciones que necesiten ejecutarse al cargar el DOM
});


function agregarDevolucion() {
    const referencia = document.getElementById('referencia-busqueda').value.trim();
    const color = document.getElementById('color').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value.trim());
    const observacion = document.getElementById('observacion').value.trim();

    if (!referencia || !color || isNaN(cantidad) || cantidad <= 0 || !observacion) {
        Swal.fire({
            icon: 'error',
            title: 'Faltan datos o la cantidad no es válida',
            text: 'Por favor complete todos los campos correctamente.',
            confirmButtonText: 'Aceptar',
        });
        return;
    }

    $.ajax({
        url: 'agregar_devolucion.php', // Archivo PHP que procesa las devoluciones
        type: 'POST',
        data: {
            referencia: referencia,
            color: color,
            cantidad: cantidad,
            observacion: observacion,
        },
        success: function(response) {
            try {
                response = typeof response === 'string' ? JSON.parse(response) : response;

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Devolución registrada con éxito',
                        confirmButtonText: 'Aceptar',
                    });
                    // Limpia los campos después de agregar la devolución
                    document.getElementById('referencia-busqueda').value = '';
                    document.getElementById('color').value = '';
                    document.getElementById('cantidad').value = '';
                    document.getElementById('observacion').value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar devolución',
                        text: response.message || 'Intente nuevamente más tarde.',
                        confirmButtonText: 'Aceptar',
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el formato de la respuesta',
                    text: 'El servidor devolvió una respuesta inválida. Intente nuevamente más tarde.',
                    confirmButtonText: 'Aceptar',
                });
                console.error('Error al analizar la respuesta JSON:', e);
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'No se pudo procesar la devolución. Intente más tarde.',
                confirmButtonText: 'Aceptar',
            });
        },
    });
}


if (!referencia || !color || isNaN(cantidad) || cantidad <= 0 || !observacion) {
    Swal.fire({
        icon: 'error',
        title: 'Faltan datos o la cantidad no es válida',
        text: 'Por favor complete todos los campos correctamente.',
        confirmButtonText: 'Aceptar',
    });
    return;
}

console.log("Datos enviados: ", {
    referencia: referencia,
    color: color,
    cantidad: cantidad,
    observacion: observacion
});  // Para verificar los datos antes de enviarlos

$.ajax({
    url: 'agregar_devolucion.php', 
    type: 'POST',
    data: {
        referencia: referencia,
        color: color,
        cantidad: cantidad,
        observacion: observacion,
    },
    success: function(response) {
        // El resto de la lógica
    },
    error: function(xhr, status, error) {
        Swal.fire({
            icon: 'error',
            title: 'Error en la solicitud',
            text: 'No se pudo procesar la devolución. Intente más tarde.',
            confirmButtonText: 'Aceptar',
        });
    },
});
