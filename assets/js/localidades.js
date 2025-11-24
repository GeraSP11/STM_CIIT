// =====================================================
//  CRUD DE LOCALIDADES
//  Secciones: 
//      1. Registrar
//      2. Consultar
//      3. Actualizar
//      4. Eliminar
//      5. Funciones reutilizables
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // ---- 1. Registrar ----
    configurarRegistro(); // validaciones internas

    // ---- 2. Consultar ----
    configurarVistaConsultar();
    consultarLocalidades();


    // ---- 3. Actualizar ----
    //actualizarLocalidades();

    // ---- 4. Eliminar ----
    eliminarLocalidades();
});


/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */


/**
 * Configura el envío del formulario de registro.
 */
function configurarRegistro() {

    const formulario = document.querySelector("#formLocalidades");
    if (!formulario) return;
    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Localidad?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("registrar-localidad", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Localidad registrada correctamente.",
                        "dashboard.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un problema en la petición.", "error"));
            });
    });
}



/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */
function configurarVistaConsultar() {
    const selectFiltros = document.getElementById("selectFiltro");
    const botonAgregarFiltro = document.getElementById("btnAddFiltro");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");

    if (!selectFiltros || !botonAgregarFiltro || !contenedorFiltros) return;

    // Evento principal: agregar filtro
    botonAgregarFiltro.addEventListener("click", () => {
        const valorFiltro = selectFiltros.value;
        const textoFiltro = selectFiltros.options[selectFiltros.selectedIndex].text;

        if (!valorFiltro) {
            alerta("Filtros", "Selecciona un filtro primero", "warning");
            return;
        }

        // Crear fila de filtro
        const fila = document.createElement("div");
        fila.classList.add("filter-row");
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Ingrese valor" required style="width: 40%;">
            <button class="delete-btn"><i class="fas fa-trash"></i></button>
        `;
        contenedorFiltros.appendChild(fila);

        // Mostrar botón consultar
        contenedorBotonConsultar.style.display = "flex";

        // Deshabilitar la opción seleccionada
        selectFiltros.querySelector(`option[value="${valorFiltro}"]`).disabled = true;

        // Configurar botón eliminar del filtro
        fila.querySelector(".delete-btn").addEventListener("click", () => {
            fila.remove();
            contenedorBotonConsultar.style.display = contenedorFiltros.children.length ? "flex" : "none";
            // Volver a habilitar la opción en el select
            selectFiltros.querySelector(`option[value="${valorFiltro}"]`).disabled = false;
        });
    });
}



function consultarLocalidades() {
    // Elementos del DOM
    const formularioConsulta = document.getElementById("formConsultarLocalidades");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");
    const contenedorResultados = document.getElementById("tablaResultados");
    const cuerpoTabla = document.querySelector("#tablaLocalidades tbody");
    const botonVolver = document.getElementById("btnVolver");

    if (!formularioConsulta) return;

    formularioConsulta.addEventListener("submit", (e) => {
        e.preventDefault(); // Evita envío real

        // Capturamos los valores de los filtros dinámicos al momento de enviar
        const nombre = contenedorFiltros.querySelector('input[name="nombre_centro_trabajo"]')?.value.trim() || "";
        const localidad = contenedorFiltros.querySelector('input[name="localidad"]')?.value.trim() || "";
        const poblacion = contenedorFiltros.querySelector('input[name="poblacion"]')?.value.trim() || "";
        const estado = contenedorFiltros.querySelector('input[name="estado"]')?.value.trim() || "";

        // Crear objeto simple con solo los filtros que tienen valor
        const filtros = {};
        if (nombre) filtros.nombre_centro_trabajo = nombre;
        if (localidad) filtros.localidad = localidad;
        if (poblacion) filtros.poblacion = poblacion;
        if (estado) filtros.estado = estado;

        apiRequest("consultar-localidades", filtros)
            .then(respuesta => respuesta.json())
            .then(datos => {
                if (!datos || datos.length === 0) {
                    alerta("Consulta Localidades", "No se encontró información con los filtros aplicados", "warning");
                    return;
                }

                // Limpiar tabla antes de mostrar nuevos datos
                cuerpoTabla.innerHTML = "";

                // Insertar filas en la tabla
                datos.forEach(localidadItem => {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                    <td>${localidadItem.nombre}</td>
                    <td>${localidadItem.ubicacion}</td>
                    <td>${localidadItem.poblacion}</td>
                    <td>${localidadItem.estado}</td>
                    <td>${localidadItem.tipo_instalacion || ""}</td>
                `;
                    cuerpoTabla.appendChild(fila);
                });

                // Mostrar resultados y ocultar formulario
                contenedorResultados.style.display = "block";
                formularioConsulta.parentElement.style.display = "none";
            })
            .catch(() => alerta("Error", "Ocurrió un problema al consultar los datos", "error"));
    });

    // Botón Volver
    botonVolver.addEventListener("click", () => {
        // Ocultar resultados y mostrar formulario
        contenedorResultados.style.display = "none";
        formularioConsulta.parentElement.style.display = "block";

        // Limpiar filtros dinámicos y tabla
        contenedorFiltros.innerHTML = "";
        contenedorBotonConsultar.style.display = "none";
        cuerpoTabla.innerHTML = "";

        // Restaurar select al estado inicial (ANTES de limpiar filtros)
        const selectFiltros = document.getElementById("selectFiltro");
        if (selectFiltros) {
            selectFiltros.selectedIndex = 0; // seleccionar la opción por defecto
            Array.from(selectFiltros.options).forEach(opt => opt.disabled = false); // habilitar todas las opciones
        }
    });
}




/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */

/* =====================================================
   3. ACTUALIZAR (UPDATE) - FUNCIONALIDAD LIMPIA
   ===================================================== */

function actualizarLocalidades() {
    // Referencias a elementos
    const inputBusqueda = document.getElementById('inputBuscarLocalidad');
    const datalistLocalidades = document.getElementById('localidades');
    const contenedorBusqueda = document.getElementById('contenedorBusqueda');
    const contenedorBotones = document.getElementById('contenedorBotones');
    const formulario = document.getElementById('formActualizarLocalidad');

    const inputId = document.getElementById('inputIdLocalidad');
    const inputIdDisplay = document.getElementById('inputIdLocalidadDisplay');
    const inputNombreCentro = document.getElementById('inputNombreCentro');
    const inputUbicacion = document.getElementById('inputUbicacion');
    const inputPoblacion = document.getElementById('inputPoblacion');
    const selectEstado = document.getElementById('estados');
    const selectTipo = document.getElementById('selectTipoInstalacion');
    const inputLocalidad = document.getElementById('inputLocalidad');

    if (!inputBusqueda) return;

    // Al escribir, buscar localidades
    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        apiRequest('buscar-localidades', { busqueda: texto })
            .then(r => r.json())
            .then(localidades => {
                // Limpiar datalist
                datalistLocalidades.innerHTML = '';
                if (!localidades || localidades.length === 0) return;

                localidades.forEach(loc => {
                    const opcion = document.createElement('option');

                    opcion.value = loc.localidad; // MOSTRAR la localidad real 

                    opcion.dataset.id = loc.id_localidad;
                    opcion.dataset.nombreCentro = loc.nombre;
                    opcion.dataset.ubicacion = loc.ubicacion;
                    opcion.dataset.poblacion = loc.poblacion;
                    opcion.dataset.estado = loc.estado;
                    opcion.dataset.tipoInstalacion = loc.tipo_instalacion;

                    opcion.dataset.localidad = loc.localidad;

                    datalistLocalidades.appendChild(opcion);
                });


            })
            .catch(() => alerta("Error", "No se pudo buscar localidades", "error"));
    });

    // Al seleccionar un valor del datalist
    inputBusqueda.addEventListener('change', () => {
        const opcionSeleccionada = Array.from(datalistLocalidades.options)
            .find(opt => opt.value === inputBusqueda.value);

        if (!opcionSeleccionada) return;

        // Llenar formulario con los datos
        inputId.value = opcionSeleccionada.dataset.id;
        inputIdDisplay.value = opcionSeleccionada.dataset.id;
        inputNombreCentro.value = opcionSeleccionada.dataset.nombreCentro;
        inputUbicacion.value = opcionSeleccionada.dataset.ubicacion;
        inputPoblacion.value = opcionSeleccionada.dataset.poblacion;
        inputLocalidad.value = opcionSeleccionada.dataset.localidad;



        // Seleccionar estado
        Array.from(selectEstado.options).forEach(opt => {
            opt.selected = opt.value === opcionSeleccionada.dataset.estado || opt.text === opcionSeleccionada.dataset.estado;
        });

        // Seleccionar tipo de instalación
        Array.from(selectTipo.options).forEach(opt => {
            opt.selected = opt.value === opcionSeleccionada.dataset.tipoInstalacion || opt.text === opcionSeleccionada.dataset.tipoInstalacion;
        });

        // Mostrar botones y ocultar búsqueda
        contenedorBusqueda.classList.add('oculto');
        contenedorBotones.style.display = 'block';
    });

    // Enviar formulario
    formulario.addEventListener('submit', e => {
        // 🔹 Validación nativa del navegador ANTES de detener el envío
        if (!formulario.checkValidity()) {
            formulario.reportValidity(); // muestra los mensajes del navegador
            return; // NO ejecuta AJAX si hay errores
        }

        e.preventDefault(); // ahora sí, detenemos el envío normal
        e.preventDefault();

        confirmar("¿Actualizar localidad?", "Se guardarán los cambios")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("actualizar-localidad", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(resp, "Localidad actualizada correctamente.", "actualizar-localidades.php"))
                    .catch(() => alerta("Error", "Ocurrió un problema al actualizar", "error"));
            });
    });
}

// function cargarDatosParaEditar(id) {
//     apiRequest("obtener_uno", { id })
//         .then(r => r.json())
//         .then(data => {
//             // Llenar formulario...
//         });
// }
//
// function guardarCambios() {
//     apiRequest("actualizar", formularioEditar)
//         .then(r => r.text())
//         .then(resp => manejarRespuestaCRUD(resp, "Actualizado correctamente."));
// }



/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */

// function eliminarPersonal(id) {
//     confirmar("¿Eliminar registro?", "No se puede deshacer.")
//         .then(r => {
//             if (!r.isConfirmed) return;
//             apiRequest("eliminar", { id })
//                 .then(r => r.text())
//                 .then(resp => manejarRespuestaCRUD(resp, "Eliminado correctamente."));
//         });
// }



/* =====================================================
   5. FUNCIONES REUTILIZABLES
   ===================================================== */

/**
 * Envía una petición POST al backend con una acción y datos.
 */
function apiRequest(accion, datos = null) {

    const formData = datos instanceof HTMLFormElement
        ? new FormData(datos)
        : new FormData();

    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) {
            formData.append(clave, datos[clave]);
        }
    }

    formData.append("action", accion);

    return fetch('/ajax/localidad-ajax.php', {
        method: "POST",
        body: formData
    });
}

/**
 * Maneja respuestas del backend para cualquier operación del CRUD.
 */
/**
 * Maneja respuestas del backend para cualquier operación del CRUD.
 */
function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {
    if (respuesta.trim() === "OK") {
        alerta("Éxito", mensajeExito, "success")
            .then(() => {
                if (redireccion) window.location.href = redireccion;
            });
    } else {
        alerta("Error", respuesta, "error");
    }
}
/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */

function eliminarLocalidades() {
    const inputBusqueda = document.getElementById('inputBuscarLocalidad');
    const datalistLocalidades = document.getElementById('localidades');
    const botonEliminar = document.getElementById('btnEliminar');

    // Verificar que los elementos existan
    if (!inputBusqueda || !botonEliminar || !datalistLocalidades) {
        console.log('Elementos no encontrados para eliminar localidades');
        return;
    }

    console.log('Función eliminarLocalidades inicializada');

    // Variable para almacenar la localidad seleccionada
    let localidadSeleccionada = null;

    // Al escribir, buscar localidades
    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        
        console.log('Texto ingresado:', texto);
        
        // Resetear selección
        localidadSeleccionada = null;
        
        // Si hay menos de 2 caracteres, limpiar y salir
        if (texto.length < 2) {
            datalistLocalidades.innerHTML = '';
            return;
        }

        // Buscar localidades
        apiRequest('buscar-localidades', { busqueda: texto })
            .then(r => r.json())
            .then(localidades => {
                console.log('Localidades encontradas:', localidades);
                
                // Limpiar datalist
                datalistLocalidades.innerHTML = '';
                
                if (!localidades || localidades.length === 0) {
                    console.log('No se encontraron localidades');
                    return;
                }

                // Agregar opciones al datalist
                localidades.forEach(loc => {
                    const opcion = document.createElement('option');
                    opcion.value = `${loc.nombre} - ${loc.localidad}`;
                    opcion.dataset.id = loc.id_localidad;
                    opcion.dataset.nombre = loc.nombre;
                    opcion.dataset.localidad = loc.localidad;
                    datalistLocalidades.appendChild(opcion);
                });
                
                console.log('Opciones agregadas al datalist:', datalistLocalidades.children.length);
            })
            .catch(error => {
                console.error('Error al buscar localidades:', error);
                alerta("Error", "No se pudo buscar localidades", "error");
            });
    });

    // Al cambiar el valor (cuando se selecciona del datalist)
    inputBusqueda.addEventListener('change', () => {
        const valorIngresado = inputBusqueda.value.trim();
        console.log('Valor seleccionado:', valorIngresado);
        
        // Buscar la opción que coincida
        const opcionEncontrada = Array.from(datalistLocalidades.options)
            .find(opt => opt.value === valorIngresado);

        if (opcionEncontrada) {
            localidadSeleccionada = {
                id: opcionEncontrada.dataset.id,
                nombre: opcionEncontrada.dataset.nombre,
                localidad: opcionEncontrada.dataset.localidad,
                textoCompleto: valorIngresado
            };
            console.log('Localidad seleccionada válida:', localidadSeleccionada);
        } else {
            localidadSeleccionada = null;
            console.log('No se encontró coincidencia exacta');
        }
    });

    // Botón eliminar
    botonEliminar.addEventListener('click', () => {
        console.log('Botón eliminar clickeado');
        console.log('Localidad almacenada:', localidadSeleccionada);
        
        // Verificar si hay una localidad seleccionada
        if (!localidadSeleccionada || !localidadSeleccionada.id) {
            alerta(
                "Selección requerida", 
                "Por favor, selecciona una localidad de la lista desplegable. Escribe al menos 2 caracteres y elige una opción que aparezca.", 
                "warning"
            );
            return;
        }

        const idLocalidad = localidadSeleccionada.id;
        const nombreLocalidad = localidadSeleccionada.textoCompleto;

        console.log('Localidad a eliminar - ID:', idLocalidad, 'Nombre:', nombreLocalidad);

        // Confirmar eliminación
        confirmar(
            "¿Eliminar Localidad?", 
            `Se eliminará: ${nombreLocalidad}. Esta acción no se puede deshacer.`
        )
            .then(r => {
                if (!r.isConfirmed) {
                    console.log('Eliminación cancelada por el usuario');
                    return;
                }

                console.log('Enviando petición de eliminación...');

                // Enviar petición de eliminación
                apiRequest("eliminar-localidad", { id_localidad: idLocalidad })
                    .then(r => r.text())
                    .then(resp => {
                        console.log('Respuesta del servidor:', resp);
                        
                        // Limpiar el input y la selección
                        inputBusqueda.value = '';
                        localidadSeleccionada = null;
                        datalistLocalidades.innerHTML = '';
                        
                        manejarRespuestaCRUD(
                            resp,
                            "Localidad eliminada correctamente.",
                            "eliminar-localidades.php" // Redirigir a la misma página
                        );
                    })
                    .catch(error => {
                        console.error('Error al eliminar:', error);
                        alerta("Error", "Ocurrió un problema al eliminar", "error");
                    });
            });
    });
}