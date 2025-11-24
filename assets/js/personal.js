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

    cargarLocalidades();
    update();



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
/* =====================================================
   3. ACTUALIZAR (UPDATE) — ALERTAS MODIFICADAS
   ===================================================== */

   function update() {
    const curpBusqueda = document.getElementById('curp_busqueda');
    const updateForm = document.getElementById('updateForm');

    if (curpBusqueda) {
        curpBusqueda.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarPersonalPorCurp(this.value.trim());
            }
        });
    }

    if (updateForm) {
        updateForm.addEventListener('submit', function (e) {
            e.preventDefault();
            actualizarPersonal();
        });
    }
}


// Buscar personal usando apiRequest + alertas personalizadas
function buscarPersonalPorCurp(curp) {
    if (!curp || curp.length !== 18) {
        alerta("CURP inválida", "Debe ingresar 18 caracteres.", "error");
        return;
    }

    apiRequest("consultar-personal", { curp })
        .then(r => r.json())
        .then(data => {
            if (!data || data.length === 0) {
                alerta("Sin resultados", "No se encontró personal con esa CURP.", "warning");
                limpiarFormulario();
                return;
            }

            const personal = data[0];

            document.getElementById('id_personal').value = personal.id_personal || '';
            document.getElementById('curp').value = personal.curp || '';
            document.getElementById('nombre_personal').value = personal.nombre || '';
            document.getElementById('apellido_paterno').value = personal.apellido_paterno || '';
            document.getElementById('apellido_materno').value = personal.apellido_materno || '';
            document.getElementById('cargo').value = personal.cargo || '';
            document.getElementById('afiliacion_laboral').value = personal.id_localidad || '';

            const formActions = document.querySelector('.form-actions');
            if (formActions) {
                formActions.style.display = 'flex';
                formActions.style.justifyContent = 'center';
                formActions.style.gap = '15px';
                formActions.style.marginTop = '30px';
            }

            alerta("Personal encontrado", "Puede editar los campos y guardar los cambios.", "success");
        })
        .catch(err => {
            console.error("Error:", err);
            alerta("Error", "Ocurrió un problema al consultar al personal.", "error");
        });
}


// Actualizar usando apiRequest + alertas personalizadas
function actualizarPersonal() {
    const form = document.getElementById('updateForm');

    const id = document.getElementById('id_personal').value;
    const curp = document.getElementById('curp').value.trim();
    const nombre = document.getElementById('nombre_personal').value.trim();
    const apellidoPaterno = document.getElementById('apellido_paterno').value.trim();
    const cargo = document.getElementById('cargo').value;
    const afiliacion = document.getElementById('afiliacion_laboral').value;

    if (!id || !curp || !nombre || !apellidoPaterno || !cargo || !afiliacion) {
        alerta("Campos incompletos", "Debe llenar todos los campos obligatorios.", "warning");
        return;
    }

    if (curp.length !== 18) {
        alerta("CURP inválida", "La CURP debe tener exactamente 18 caracteres.", "warning");
        return;
    }

    apiRequest("actualizar-personal", form)
        .then(r => r.text())
        .then(resp => {
            manejarRespuestaCRUD(resp, "Personal actualizado correctamente.");
            limpiarFormulario();
        })
        .catch(err => {
            console.error("Error:", err);
            alerta("Error", "No se pudo actualizar el personal.", "error");
        });
}


function limpiarFormulario() {
    document.getElementById('updateForm').reset();
    document.getElementById('id_personal').value = '';
    document.getElementById('curp_busqueda').value = '';

    const formActions = document.querySelector('.form-actions');
    if (formActions) formActions.style.display = 'none';
}





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
