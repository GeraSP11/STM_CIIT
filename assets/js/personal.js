// =====================================================
//  CRUD DE PERSONAL
//  Secciones: 
//      1. Registrar
//      2. Consultar
//      3. Actualizar
//      4. Eliminar
//      5. Funciones reutilizables
// =====================================================

document.addEventListener("DOMContentLoaded", function () {

    const formularioRegistro = document.querySelector("form");
    const selectLocalidad = document.getElementById("afiliacion_laboral");

    if (!formularioRegistro) return;

    // ---- 1. Registrar ----
    cargarLocalidades(selectLocalidad);
    configurarRegistro(formularioRegistro);

    // ---- 2. Consultar ----
    // cargarListadoPersonal();   // (Próximo)

    // ---- 3. Actualizar ----
    // configurarEdicion();       // (Próximo)

    // ---- 4. Eliminar ----
    // configurarEliminacion();   // (Próximo)
});


/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */

/**
 * Carga las localidades necesarias para registrar personal.
 */
function cargarLocalidades(selectLocalidad) {

    apiRequest("obtener-localidades")
        .then(r => r.json())
        .then(localidades => {
            selectLocalidad.length = 1; // Mantener la opción por defecto
            localidades.forEach(loc => {
                const opcion = document.createElement("option");
                opcion.value = loc.id_localidad;
                opcion.textContent = loc.nombre_centro_trabajo;
                selectLocalidad.appendChild(opcion);
            });
        });
}

/**
 * Configura el envío del formulario de registro.
 */
function configurarRegistro(formulario) {

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Personal?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("registrar", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Registrado correctamente.",
                        "dashboard.php"
                    ));
            });
    });
}



/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */

// function cargarListadoPersonal() {
//     apiRequest("consultar")
//         .then(r => r.json())
//         .then(data => {
//             // Pintar tabla...
//         });
// }



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

    return fetch('/ajax/personal-ajax.php', {
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
