// Función para buscar cliente y autocompletar nombre
function buscarCliente() {
    const cedula = document.getElementById('cedula').value;

    if (cedula.trim() === '') {
        Swal.fire({
            icon: 'error',
            title: 'Por favor introduce la cédula',
            confirmButtonText: 'Aceptar',
        });
        return;
    }

    $.ajax({
        url: 'buscar_cliente.php',
        type: 'GET',
        data: { cedula: cedula },
        success: function(data) {
            data = typeof data === 'string' ? JSON.parse(data) : data;

            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: data.error,
                    confirmButtonText: 'Registrar Cliente',
                    text: '¿Deseas registrar un nuevo cliente?',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = 'agregar_usuario_asesor.php';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Cliente encontrado satisfactoriamente',
                    confirmButtonText: 'Aceptar',
                });

                document.getElementById('nombre').value = data.nombre;
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'Ocurrió un error al intentar buscar el cliente. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar',
            });
        }
    });
}





// Variables globales para almacenar el precio según el tipo de compra
let precioMayor;
let precioDetal;

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



function buscarDisponibilidad() {
    let referencia = document.getElementById("referencia-busqueda").value;

    if (referencia.trim() === "") {
        alert("Por favor, ingrese una referencia.");
        return;
    }

    // Petición AJAX para obtener disponibilidad
    $.ajax({
        url: "buscar_disponibilidad.php",
        type: "POST",
        data: { referencia: referencia },
        dataType: "json",
        success: function(response) {
            let tablaBody = document.querySelector("#tabla-disponibilidad tbody");
            tablaBody.innerHTML = ""; // Limpiar tabla antes de agregar nueva información

            if (response.length === 0) {
                let row = `<tr><td colspan="3">No hay disponibilidad para esta referencia</td></tr>`;
                tablaBody.innerHTML = row;
            } else {
                response.forEach(item => {
                    let row = `<tr>
                        <td>${item.ref}</td>
                        <td>${item.color}</td>
                        <td>${item.cantidad}</td>
                    </tr>`;
                    tablaBody.innerHTML += row;
                });
            }
        },
        error: function() {
            alert("Error al buscar disponibilidad.");
        }
    });
}


// Función para seleccionar precio al por mayor
function seleccionarMayor() {
    if (precioMayor) {
        // Asignamos el precio formateado
        document.getElementById('precio-unitario').value = `$ ${precioMayor.toLocaleString('es-CO')}`;
        calcularSubtotal(precioMayor);  // Llamar a calcularSubtotal con el precioMayor
    }
}

// Función para seleccionar precio al detal
function seleccionarDetal() {
    if (precioDetal) {
        // Asignamos el precio formateado
        document.getElementById('precio-unitario').value = `$ ${precioDetal.toLocaleString('es-CO')}`;
        calcularSubtotal(precioDetal);  // Llamar a calcularSubtotal con el precioDetal
    }
}

// Función para calcular el subtotal basado en cantidad y precio unitario
function calcularSubtotal(precioUnitario) {
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    
    // Aseguramos que el precioUnitario también sea un número válido
    precioUnitario = parseFloat(precioUnitario.replace(/[^\d.-]/g, '')) || 0;

    // Verificamos que tanto el precioUnitario como la cantidad sean válidos
    if (!isNaN(precioUnitario) && cantidad > 0 && precioUnitario > 0) {
        const subtotal = cantidad * precioUnitario;

        // Mostrar el subtotal con formato de moneda y separadores de miles
        document.getElementById('subtotal').value = `$ ${subtotal.toLocaleString('es-CO')}`;
    } else {
        // Si no hay cantidad o precio, se deja el campo vacío
        document.getElementById('subtotal').value = '';
    }
}

// Evento para actualizar el subtotal cuando cambia la cantidad
document.getElementById('cantidad').addEventListener('input', function() {
    const precioUnitarioText = document.getElementById('precio-unitario').value;
    // Eliminar el símbolo '$' y las comas para obtener solo el número
    const precioUnitario = parseFloat(precioUnitarioText.replace(/[^\d.-]/g, ''));
    
    if (!isNaN(precioUnitario)) {
        calcularSubtotal(precioUnitario);
    }
});

// Función para mantener el subtotal visible al salir del campo
document.getElementById('cantidad').addEventListener('blur', function() {
    const precioUnitarioText = document.getElementById('precio-unitario').value;
    const precioUnitario = parseFloat(precioUnitarioText.replace(/[^\d.-]/g, ''));
    calcularSubtotal(precioUnitario);
});

// Función para actualizar el precio unitario y el subtotal al cambiar la cantidad
document.getElementById('cantidad').addEventListener('change', function() {
    const precioUnitarioText = document.getElementById('precio-unitario').value;
    const precioUnitario = parseFloat(precioUnitarioText.replace(/[^\d.-]/g, ''));

    if (!isNaN(precioUnitario)) {
        calcularSubtotal(precioUnitario);
    }
});


// Agregar producto a la lista de productos
let totalPedido = 0;

function agregarProducto(event) {
    const referencia = document.getElementById('referencia-busqueda').value;
    const color = document.getElementById('color').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const precioUnitario = parseFloat(document.getElementById('precio-unitario').value.replace(/[$,.]/g, '').trim());
    const subtotal = cantidad * precioUnitario;
    const cedula = document.getElementById('cedula').value;
    const boton = event?.target;
    if (boton) boton.disabled = true;



    if (!referencia || !color || color === 'Seleccione un color' || isNaN(cantidad) || cantidad <= 0 || isNaN(precioUnitario) || precioUnitario <= 0 || subtotal < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Por favor completa todos los campos correctamente antes de agregar el producto',
            confirmButtonText: 'Aceptar',
        });
        return;
    }

    $.ajax({
        url: 'reservar_inventario.php',
        type: 'POST',
        data: { ref: referencia, color: color, cantidad: cantidad, cedula: cedula },
        success: function(data) {
            data = typeof data === 'string' ? JSON.parse(data) : data;
        
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al reservar',
                    text: data.error,
                    confirmButtonText: 'Aceptar',
                    
                });
                if (boton) boton.disabled = false; // ✅ aquí
                return;
            }
        
            // Si reserva fue exitosa, agregar a tabla
            const tabla = document.getElementById('productos').getElementsByTagName('tbody')[0];
            const fila = tabla.insertRow();
            fila.innerHTML = `
                <td>${referencia}</td>
                <td>${color}</td>
                <td>${cantidad}</td>
                <td>${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(precioUnitario)}</td>
                <td>${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(subtotal)}</td>
                <td><button class="btn-eliminar" onclick="eliminarFila(this, ${subtotal})">X</button></td>
            `;
        
            totalPedido += subtotal;
            document.getElementById('total-pedido').value = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(totalPedido);
        
            // Limpiar campos
            document.getElementById('referencia-busqueda').value = '';
            document.getElementById('color').value = 'Seleccione un color';
            document.getElementById('cantidad').value = '';
            document.getElementById('precio-unitario').value = '';
        
            Swal.fire({
                icon: 'success',
                title: 'Producto agregado correctamente',
                confirmButtonText: 'Aceptar',
            });
            if (boton) boton.disabled = false; // ✅ aquí

        },
        
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'Ocurrió un error al verificar el inventario. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar',
            });
            if (boton) boton.disabled = false; // ✅ aquí

        }
    });
}

function eliminarFila(btn, subtotal) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Este producto será eliminado del pedido.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            const fila = btn.closest('tr');
            const cells = fila.getElementsByTagName('td');

            const ref = cells[0].textContent;       // Asegúrate que la columna 0 sea ref
            const color = cells[1].textContent;
            const cantidad = parseInt(cells[2].textContent);
            const cedula = document.getElementById('cedula').value;

            // AJAX para liberar reserva en BD
            $.ajax({
                url: 'eliminar_reserva.php',
                type: 'POST',
                data: {
                    ref: ref,
                    color: color,
                    cantidad: cantidad,
                    cedula: cedula
                },
                success: function(response) {
                    response = typeof response === 'string' ? JSON.parse(response) : response;

                    if (response.success) {
                        // Ahora sí, eliminar la fila y actualizar total
                        fila.remove();

                        totalPedido -= subtotal;
                        document.getElementById('total-pedido').value = new Intl.NumberFormat('es-CO', {
                            style: 'currency',
                            currency: 'COP',
                            minimumFractionDigits: 0
                        }).format(totalPedido);

                        Swal.fire({
                            icon: 'success',
                            title: 'Producto eliminado correctamente',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'No se pudo devolver el producto al inventario.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la solicitud',
                        text: 'No se pudo contactar al servidor para liberar la reserva.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}


function buscarPedidos() {
    console.log('Función buscarPedidos llamada');
    const cedula = document.getElementById('cedula').value;

    $.ajax({
        url: 'buscar_pedidos.php',
        type: 'GET',
        data: { cedula: cedula },
        success: function(data) {
            console.log("Datos recibidos:", data);
            try {
                const pedidos = JSON.parse(data);
                console.log("Cantidad de pedidos encontrados:", pedidos.length);

                const tablaPedidos = document.getElementById("tabla-pedidos");
                if (tablaPedidos) {
                    if (pedidos.length > 0) {
                        let contenido = '';
                        pedidos.forEach(pedido => {
                            contenido += `<tr>
                                <td>${pedido.id_pedido}</td>
                                <td>${pedido.fecha_pedido}</td>
                                <td>${pedido.fecha_limite || 'N/A'}</td>
                                <td>${pedido.estado}</td> <!-- Muestra el estado del pedido -->
                            </tr>`;
                        });
                        tablaPedidos.querySelector('tbody').innerHTML = contenido;
                    } else {
                        // Alerta de que no hay pedidos nuevos
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin pedidos',
                            text: 'Este cliente no tiene pedidos nuevos. Este sería el primero, así que se agregarán 8 días de límite de separado.',
                            confirmButtonText: 'Aceptar',
                        });

                        // Mostrar mensaje en la tabla
                        tablaPedidos.querySelector('tbody').innerHTML = "<tr><td colspan='4'>No se encontraron pedidos.</td></tr>";
                    }
                }
            } catch (e) {
                console.error("Error al parsear los datos:", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al procesar los pedidos.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud: " + error);
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'Ocurrió un error al intentar buscar los pedidos. Por favor intenta nuevamente.',
                confirmButtonText: 'Aceptar',
            });
        }
    });
}



document.addEventListener("DOMContentLoaded", function() {
    // Aquí puedes agregar otras funciones que necesiten ejecutarse al cargar el DOM
});

// Función para crear el pedido
function crearPedido() {
    const btnCrear = document.getElementById('btn-crear-pedido');
    if (btnCrear) btnCrear.disabled = true; // 🔒 Bloquear el botón

    const cedula = document.getElementById('cedula').value;
    const nombre = document.getElementById('nombre').value;
    const asesor = document.getElementById('asesor').value;
    const medioConocimiento = document.getElementById('medio_conocimiento').value;
    const envio = document.getElementById('envio').value;
    const fecha = document.getElementById('fecha').value;
    const fechaLimite = document.getElementById('fechaLimite').value;

    if (totalPedido === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No hay productos en el pedido',
            confirmButtonText: 'Aceptar',
        });
        if (btnCrear) btnCrear.disabled = false; // 🔓 Volver a habilitar si hay error
        return;
    }

    const productos = Array.from(document.querySelectorAll('#productos tbody tr')).map(row => {
        const cells = row.getElementsByTagName('td');
        return {
            referencia: cells[0].textContent,
            color: cells[1].textContent,
            cantidad: parseInt(cells[2].textContent),
            precioUnitario: parseFloat(cells[3].textContent.replace(/[^\d,.-]/g, '').replace(/\./g, '').replace(/,/g, '.')),
            subtotal: parseFloat(cells[4].textContent.replace(/[^\d,.-]/g, '').replace(/\./g, '').replace(/,/g, '.'))
        };
    });

    $.ajax({
        url: 'crear_pedido.php',
        method: 'POST',
        data: {
            cedula: cedula,
            nombre: nombre,
            asesor: asesor,
            envio: envio,
            medio_conocimiento: medioConocimiento,
            total: totalPedido,
            productos: JSON.stringify(productos),
            fecha: fecha,
            fechaLimite: fechaLimite,
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido creado con éxito',
                    text: response.message,
                    confirmButtonText: 'Aceptar',
                }).then(() => location.reload());
            } else {
                if (btnCrear) btnCrear.disabled = false; // 🔓 Volver a habilitar si hubo error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Ocurrió un error al crear el pedido.',
                    confirmButtonText: 'Aceptar',
                });
            }
        },
        error: function(xhr, status, error) {
            if (btnCrear) btnCrear.disabled = false; // 🔓 Volver a habilitar si hubo error
            Swal.fire({
                icon: 'error',
                title: 'Error en la solicitud',
                text: 'Ocurrió un error inesperado. Revisa la consola para más detalles.',
                confirmButtonText: 'Aceptar',
            });
        }
    });
}
