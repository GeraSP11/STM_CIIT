// =====================================================
//  CRUD DE Rutas
//  Secciones: 
//      1. Registrar
//      2. Consultar
//      3. Actualizar
//      4. Eliminar
//      5. Funciones reutilizables
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // ---- 1. Registrar ----
    

    // ---- 2. Consultar ----
    

    // ---- 3. Actualizar ----
    actualizarRutas();

    // ---- 4. Eliminar ----
    
});













































// =====================================================
// FUNCIONALIDAD ACTUALIZAR RUTA
// =====================================================
function actualizarRutas() {

    // --- 3.1 Filtro de búsqueda ---
    const btnFiltroActualizar       = document.getElementById("btn-filtro-actualizar");
    const inputIdRutaActualizar     = document.getElementById("input-id-ruta-actualizar");
    const tablaResultadosActualizar = document.getElementById("tabla-resultados-actualizar");

    if (btnFiltroActualizar) {
        btnFiltroActualizar.addEventListener("click", function () {
            const idRuta = inputIdRutaActualizar.value.trim();

            apiRequest("buscar_rutas", { id_ruta: idRuta })
                .then(res => res.json())
                .then(data => renderizarResultadosActualizar(data))
                .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
        });
    }

    // --- 3.2 Botón Actualizar (abre formulario con datos precargados) ---
    const btnActualizar = document.getElementById("btn-actualizar");

    if (btnActualizar) {
        btnActualizar.addEventListener("click", function () {
            const seleccionado = document.querySelector(".checkbox-ruta-actualizar:checked");

            if (!seleccionado) {
                alerta("Advertencia", "Debe seleccionar una ruta para actualizar.", "warning");
                return;
            }

            apiRequest("obtener_ruta", { id_ruta: seleccionado.value })
                .then(res => res.json())
                .then(ruta => {
                    precargarFormularioActualizar(ruta);
                    mostrarSeccion("seccion-form-actualizar");
                })
                .catch(() => alerta("Error", "No se pudieron cargar los datos de la ruta.", "error"));
        });
    }

    // --- 3.3 Guardar cambios ---
    const formActualizar = document.getElementById("form-actualizar-ruta");

    if (formActualizar) {
        formActualizar.addEventListener("submit", function (e) {
            e.preventDefault();

            if (!validarFormularioActualizar()) return;

            apiRequest("actualizar_ruta", formActualizar)
                .then(res => res.text())
                .then(respuesta => {
                    manejarRespuestaCRUD(
                        respuesta,
                        "La ruta fue actualizada correctamente.",
                        "/rutas/consultar"
                    );
                })
                .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
        });
    }

    // --- 3.4 Botón Cancelar del formulario ---
    const btnCancelarFormActualizar = document.getElementById("btn-cancelar-form-actualizar");

    if (btnCancelarFormActualizar) {
        btnCancelarFormActualizar.addEventListener("click", function () {
            mostrarSeccion("seccion-busqueda-actualizar");
        });
    }
}


// --- Renderizar resultados de búsqueda ---
function renderizarResultadosActualizar(rutas) {
    const tablaResultadosActualizar = document.getElementById("tabla-resultados-actualizar");
    const tbody = tablaResultadosActualizar?.querySelector("tbody");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (!rutas || rutas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">No se encontraron resultados.</td></tr>`;
        return;
    }

    rutas.forEach(ruta => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><input type="checkbox" class="checkbox-ruta-actualizar" value="${ruta.id_ruta}"></td>
            <td>${ruta.id_ruta}</td>
            <td>${ruta.modalidad}</td>
        `;
        tbody.appendChild(tr);
    });

    // Solo una selección a la vez
    tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(cb => {
        cb.addEventListener("change", function () {
            tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(other => {
                if (other !== cb) other.checked = false;
            });
        });
    });
}

// --- Precargar formulario ---
function precargarFormularioActualizar(ruta) {
    document.getElementById("act-id-ruta").value            = ruta.id_ruta           ?? "";
    document.getElementById("act-localidad-origen").value   = ruta.localidad_origen  ?? "";
    document.getElementById("act-localidad-destino").value  = ruta.localidad_destino ?? "";
    document.getElementById("act-modalidad").value          = ruta.modalidad         ?? "";
    document.getElementById("act-distancia").value          = ruta.distancia         ?? "";
    document.getElementById("act-peso-soportado").value     = ruta.peso_soportado    ?? "";
}

// --- Validación del formulario ---
function validarFormularioActualizar() {
    const idRuta           = document.getElementById("act-id-ruta").value.trim();
    const localidadOrigen  = document.getElementById("act-localidad-origen").value.trim();
    const localidadDestino = document.getElementById("act-localidad-destino").value.trim();
    const modalidad        = document.getElementById("act-modalidad").value.trim();
    const distancia        = document.getElementById("act-distancia").value.trim();
    const pesoSoportado    = document.getElementById("act-peso-soportado").value.trim();

    if (!idRuta) {
        alerta("Error", "El identificador de ruta es requerido.", "error");
        return false;
    }
    if (idRuta.length > 20) {
        alerta("Error", "El ID de ruta no puede exceder 20 caracteres.", "error");
        return false;
    }
    if (!localidadOrigen || !localidadDestino) {
        alerta("Error", "Las localidades de origen y destino son requeridas.", "error");
        return false;
    }
    if (!modalidad) {
        alerta("Error", "La modalidad es requerida.", "error");
        return false;
    }
    if (distancia !== "" && isNaN(Number(distancia))) {
        alerta("Error", "La distancia debe ser un valor numérico.", "error");
        return false;
    }
    if (pesoSoportado !== "" && isNaN(Number(pesoSoportado))) {
        alerta("Error", "El peso soportado debe ser un valor numérico.", "error");
        return false;
    }

    return true;
}







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

    return fetch('/ajax/rutas-ajax.php', {
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
