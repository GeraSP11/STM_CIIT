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
