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
    // ---- 1. Registrar ----
    cargarLocalidades();
    configurarRegistro(); // validaciones internas

    // ---- 2. Consultar ----
    consultarPersonal(); // validaciones internas



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
function cargarLocalidades() {
    const selectLocalidad = document.getElementById("afiliacion_laboral");
    if (!selectLocalidad) return;
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
        })
        .catch(err => console.error("Error al cargar localidades:", err));
}

/**
 * Configura el envío del formulario de registro.
 */
function configurarRegistro() {

    const formulario = document.querySelector("#formRegistro");
    if (!formulario) return;
    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Personal?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("registrar", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Personal registrado correctamente.",
                        "dashboard.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un problema en la petición.", "error"));
            });
    });
}



/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */

function consultarPersonal() {
    const formConsulta = document.getElementById("formConsulta");
    const inputCurp = document.getElementById("curp");
    const divConsulta = document.getElementById("consultaCurp");
    const divTabla = document.getElementById("tablaResultados");
    const tbody = document.querySelector("#tablaPersonal tbody");
    const btnVolver = document.getElementById("btnVolver");

    if (!formConsulta) return;

    formConsulta.addEventListener("submit", (e) => {
        e.preventDefault(); // Evita envío real
        // Si llegó aquí, la validación HTML ya pasó
        const curp = inputCurp.value.trim();

        console.log(curp);
        apiRequest("consultar-personal", { curp })
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) {
                    alerta("Consulta Personal", "No se encontró información para esa CURP", "warning");
                    return;
                }

                tbody.innerHTML = "";
                data.forEach(personal => {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${personal.nombre}</td>
                        <td>${personal.apellido_paterno}</td>
                        <td>${personal.apellido_materno}</td>
                        <td>${personal.afiliacion_laboral}</td>
                        <td>${personal.cargo || ""}</td>
                        <td>${personal.curp}</td>
                    `;
                    tbody.appendChild(fila);
                });

                divConsulta.style.display = "none";
                divTabla.style.display = "block";
            })
            .catch(() => alerta("Error", "Ocurrió un problema al consultar los datos", "error"));
    });

    /**
     * console.log(curp);
        apiRequest("consultar-personal", { curp })
            .then(r => r.text())
            .then(texto => {
                console.log("Respuesta cruda:", texto);
                try {
                    const data = JSON.parse(texto);
                    // procesar data...
                } catch (e) {
                    console.error("No es JSON válido:", e);
                }
            });
        ;
     */

    btnVolver.addEventListener("click", () => {
        divTabla.style.display = "none";
        divConsulta.style.display = "block";
        inputCurp.value = "";
        tbody.innerHTML = "";
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
