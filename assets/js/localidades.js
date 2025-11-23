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
    // configurarEdicion();       // (Próximo)

    // ---- 4. Eliminar ----
    // configurarEliminacion();   // (Próximo)
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
