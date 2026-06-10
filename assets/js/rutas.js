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
    
    // Inicializar tooltips de Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ---- 1. Registrar ----
    registrarRuta();
    
    // ---- 2. Consultar ----
    consultarRutas();

    // ---- 3. Actualizar ----
    actualizarRutas();

    // ---- 4. Eliminar ----
});

// =====================================================
// FUNCIONALIDAD REGISTRAR RUTA
// =====================================================
function registrarRuta() {

    const formRegistrar = document.getElementById("form-registrar-ruta");
    if (!formRegistrar) return;

    const selOrigen    = document.getElementById("reg-localidad-origen");
    const selDestino   = document.getElementById("reg-localidad-destino");
    const selModalidad = document.getElementById("reg-modalidad");
    const selTipoRuta  = document.getElementById("reg-tipo-ruta");
    const grupoTipo    = document.getElementById("grupo-tipo-ruta");
    const inputPeso    = document.getElementById("reg-peso-soportado");
    const btnLimpiar   = document.getElementById("btn-limpiar-ruta");

    // --- 1.1 Cargar localidades en ambos selects ---
    apiRequest("obtener_localidades")
        .then(res => res.json())
        .then(localidades => {
            if (!Array.isArray(localidades)) {
                alerta("Error", "No se pudieron cargar las localidades.", "error");
                return;
            }

            // Opción vacía inicial
            selOrigen.innerHTML  = '<option value="" selected disabled>Seleccione origen...</option>';
            selDestino.innerHTML = '<option value="" selected disabled>Seleccione destino...</option>';

            localidades.forEach(loc => {
                const texto = loc.nombre_centro_trabajo
                    ? `${loc.nombre_centro_trabajo} — ${loc.localidad}, ${loc.estado}`
                    : `${loc.localidad}, ${loc.estado}`;

                selOrigen.appendChild(new Option(texto, loc.id_localidad));
                selDestino.appendChild(new Option(texto, loc.id_localidad));
            });
        })
        .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));

    // --- 1.2 Mostrar/ocultar Tipo de Ruta según Modalidad ---
    selModalidad.addEventListener("change", function () {
        const modalidad = this.value;

        if (modalidad === "Carretera") {
            grupoTipo.style.display = "block";
            selTipoRuta.required    = true;
        } else {
            grupoTipo.style.display = "none";
            selTipoRuta.required    = false;
            selTipoRuta.value       = "";
        }

        // Limpiar peso al cambiar modalidad para evitar inconsistencias
        inputPeso.value = "";
        inputPeso.removeAttribute("max");
    });

    // --- 1.3 Validar peso según tipo de ruta al cambiar tipo ---
    selTipoRuta.addEventListener("change", function () {
        const tipo = this.value;
        inputPeso.removeAttribute("max");

        if (tipo === "B") {
            inputPeso.setAttribute("max", "38");
            inputPeso.placeholder = "Máx. 38 ton";
        } else if (tipo === "C") {
            inputPeso.setAttribute("max", "25.5");
            inputPeso.placeholder = "Máx. 25.5 ton";
        } else {
            inputPeso.placeholder = "Ej. 38";
        }

        // Si ya hay un peso ingresado, re-validar en tiempo real
        if (inputPeso.value !== "") {
            validarPesoTipoRuta(tipo, parseFloat(inputPeso.value));
        }
    });

    // --- 1.4 Validar peso en tiempo real al escribir ---
    inputPeso.addEventListener("input", function () {
        const tipo = selTipoRuta.value;
        const peso = parseFloat(this.value);
        if (tipo && !isNaN(peso)) {
            validarPesoTipoRuta(tipo, peso, /* silencioso */ true);
        }
    });

    // --- 1.5 Envío del formulario ---
    formRegistrar.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!validarFormularioRegistro()) return;

        apiRequest("registrar_ruta", formRegistrar)
            .then(res => res.text())
            .then(respuesta => {
                manejarRespuestaCRUD(
                    respuesta,
                    "Ruta registrada correctamente.",
                    null
                );
                
                formRegistrar.reset();
            })
            .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
    });

    // --- 1.6 Botón Limpiar ---
    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", function () {
            formRegistrar.reset();
            selOrigen.value    = "";
            selDestino.value   = "";
            selModalidad.value = "";
            selTipoRuta.value  = "";
            grupoTipo.style.display = "none";
            inputPeso.removeAttribute("max");
            inputPeso.placeholder = "Ej. 38";
        });
    }
}

// --- Validación completa del formulario de registro ---
function validarFormularioRegistro() {
    const localidadOrigen  = document.getElementById("reg-localidad-origen").value;
    const localidadDestino = document.getElementById("reg-localidad-destino").value;
    const modalidad        = document.getElementById("reg-modalidad").value;
    const tipoRuta         = document.getElementById("reg-tipo-ruta").value;
    const distancia        = document.getElementById("reg-distancia").value.trim();
    const peso             = document.getElementById("reg-peso-soportado").value.trim();

    // Localidad origen obligatoria
    if (!localidadOrigen) {
        alerta("Error", "La localidad de origen es obligatoria.", "error");
        return false;
    }

    // Localidad destino obligatoria y diferente al origen
    if (!localidadDestino) {
        alerta("Error", "La localidad de destino es obligatoria.", "error");
        return false;
    }
    if (localidadOrigen === localidadDestino) {
        alerta("Error", "La localidad de destino debe ser distinta a la de origen.", "error");
        return false;
    }

    // Modalidad obligatoria
    if (!modalidad) {
        alerta("Error", "Debe seleccionar una modalidad válida.", "error");
        return false;
    }

    // Tipo de ruta obligatorio solo si la modalidad es Carretera
    if (modalidad === "Carretera" && !tipoRuta) {
        alerta("Error", "El tipo de ruta es obligatorio para modalidad Carretera.", "error");
        return false;
    }

    // Distancia: mayor a 0 si se ingresa
    if (distancia !== "") {
        const distNum = parseFloat(distancia);
        if (isNaN(distNum) || distNum <= 0) {
            alerta("Error", "La distancia debe ser un número mayor a 0.", "error");
            return false;
        }
    }

    // Peso soportado: mayor a 0 y respetando límites por tipo de ruta
    if (peso !== "") {
        const pesoNum = parseFloat(peso);
        if (isNaN(pesoNum) || pesoNum <= 0) {
            alerta("Error", "El peso soportado debe ser un valor válido mayor a 0.", "error");
            return false;
        }
        if (!validarPesoTipoRuta(tipoRuta, pesoNum)) {
            return false;
        }
    }

    return true;
}

// --- Valida el peso según tipo de ruta; retorna true si es válido ---
function validarPesoTipoRuta(tipoRuta, peso, silencioso = false) {
    let limiteMax = null;

    if (tipoRuta === "B") {
        limiteMax = 38;
    } else if (tipoRuta === "C") {
        limiteMax = 25.5;
    }

    if (limiteMax !== null && peso > limiteMax) {
        if (!silencioso) {
            alerta(
                "Error",
                `El peso del vehículo excede el límite permitido para la ruta seleccionada. ` +
                `Rutas tipo ${tipoRuta} permiten un máximo de ${limiteMax} toneladas.`,
                "error"
            );
        }
        return false;
    }

    return true;
}

// =====================================================
// FUNCIONALIDAD CONSULTAR RUTA
// =====================================================
function consultarRutas() {

    // Verificar que estamos en la página de consulta
    const selOrigen  = document.getElementById("sel-origen");
    const selDestino = document.getElementById("sel-destino");
    if (!selOrigen || !selDestino) return;

    // --- 2.1 Cargar localidades en ambos selects ---
    apiRequest("obtener_localidades")
        .then(res => res.json())
        .then(localidades => {
            if (!Array.isArray(localidades)) {
                alerta("Error", "No se pudieron cargar las localidades.", "error");
                return;
            }
            localidades.forEach(loc => {
                const texto = loc.nombre_centro_trabajo
                    ? `${loc.nombre_centro_trabajo} — ${loc.localidad}, ${loc.estado}`
                    : `${loc.localidad}, ${loc.estado}`;

                const optOrigen  = new Option(texto, loc.id_localidad);
                const optDestino = new Option(texto, loc.id_localidad);
                selOrigen.appendChild(optOrigen);
                selDestino.appendChild(optDestino);
            });
        })
        .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));

    // --- 2.2 Botón Consultar ---
    const btnConsultar = document.getElementById("btn-consultar");

    if (btnConsultar) {
        btnConsultar.addEventListener("click", function () {
            const idOrigen  = selOrigen.value;
            const idDestino = selDestino.value;

            if (!idOrigen && !idDestino) {
                alerta("Advertencia", "Seleccione al menos una localidad para filtrar.", "warning");
                return;
            }

            apiRequest("buscar_rutas_consulta", {
                id_origen:  idOrigen,
                id_destino: idDestino
            })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alerta("Error", data.error, "error");
                        return;
                    }
                    renderizarResultadosConsulta(data);
                    mostrarSeccion("seccion-resultados");
                })
                .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
        });
    }

    // --- 2.3 Botón Ver detalle ---
    const btnVerDetalle = document.getElementById("btn-ver-detalle");

    if (btnVerDetalle) {
        btnVerDetalle.addEventListener("click", function () {
            const seleccionado = document.querySelector(".radio-ruta-consulta:checked");

            if (!seleccionado) {
                alerta("Advertencia", "Debe seleccionar una ruta para ver el detalle.", "warning");
                return;
            }

            apiRequest("obtener_ruta_detalle", { id_ruta: seleccionado.value })
                .then(res => res.json())
                .then(ruta => {
                    if (ruta.error) {
                        alerta("Error", ruta.error, "error");
                        return;
                    }
                    precargarDetalleConsulta(ruta);
                    mostrarSeccion("seccion-detalle");
                })
                .catch(() => alerta("Error", "No se pudieron cargar los datos de la ruta.", "error"));
        });
    }

    // --- 2.4 Regresar a filtros ---
    const btnRegresarFiltros = document.getElementById("btn-regresar-filtros");

    if (btnRegresarFiltros) {
        btnRegresarFiltros.addEventListener("click", function () {
            mostrarSeccion("seccion-filtros");
        });
    }

    // --- 2.5 Regresar a resultados ---
    const btnRegresarResultados = document.getElementById("btn-regresar-resultados");

    if (btnRegresarResultados) {
        btnRegresarResultados.addEventListener("click", function () {
            mostrarSeccion("seccion-resultados");
        });
    }
}

// --- Renderizar tabla de resultados ---
function renderizarResultadosConsulta(rutas) {
    const tbody = document.getElementById("tbody-resultados");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (!rutas || rutas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-3">
                    No se encontraron rutas con los filtros seleccionados.
                </td>
            </tr>`;
        return;
    }

    rutas.forEach(ruta => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td class="text-center">
                <input type="radio" name="ruta-consulta" class="radio-ruta-consulta" value="${ruta.id_ruta}">
            </td>
            <td>${ruta.id_ruta}</td>
            <td>${ruta.nombre_origen ?? "—"}</td>
            <td>${ruta.nombre_destino ?? "—"}</td>
            <td>${ruta.modalidad_ruta ?? "—"}</td>
            <td>${ruta.tipo_ruta ?? "—"}</td>
            <td>${ruta.distancia != null ? ruta.distancia : "—"}</td>
        `;

        // Seleccionar fila al hacer clic en cualquier parte
        tr.addEventListener("click", function () {
            document.querySelectorAll("#tbody-resultados tr").forEach(r => r.classList.remove("tr-seleccionada"));
            tr.classList.add("tr-seleccionada");
            tr.querySelector(".radio-ruta-consulta").checked = true;
        });

        tbody.appendChild(tr);
    });
}

// --- Precargar campos de detalle ---
function precargarDetalleConsulta(ruta) {
    document.getElementById("det-id-ruta").value    = ruta.id_ruta          ?? "—";
    document.getElementById("det-origen").value     = ruta.nombre_origen    ?? "—";
    document.getElementById("det-destino").value    = ruta.nombre_destino   ?? "—";
    document.getElementById("det-modalidad").value  = ruta.modalidad_ruta   ?? "—";
    document.getElementById("det-tipo").value       = ruta.tipo_ruta        ?? "—";
    document.getElementById("det-distancia").value  = ruta.distancia        ?? "—";
    document.getElementById("det-peso").value       = ruta.peso_soportado   ?? "—";
    document.getElementById("det-descripcion").value = ruta.descripcion     ?? "";
}

// --- Mostrar sección y ocultar las demás ---
function mostrarSeccion(idSeccion) {
    const secciones = ["seccion-filtros", "seccion-resultados", "seccion-detalle"];
    secciones.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (id === idSeccion) {
            el.classList.remove("hidden");
        } else {
            el.classList.add("hidden");
        }
    });
    window.scrollTo({ top: 0, behavior: "smooth" });
}


// =====================================================
// FUNCIONALIDAD ACTUALIZAR RUTA
// =====================================================
function actualizarRutas() {

    const btnFiltroActualizar       = document.getElementById("btn-filtro-actualizar");
    const inputIdRutaActualizar     = document.getElementById("input-id-ruta-actualizar");

    if (btnFiltroActualizar) {
        btnFiltroActualizar.addEventListener("click", function () {
            const idRuta = inputIdRutaActualizar.value.trim();

            apiRequest("buscar_rutas", { id_ruta: idRuta })
                .then(res => res.json())
                .then(data => renderizarResultadosActualizar(data))
                .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
        });
    }

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

    const btnCancelarFormActualizar = document.getElementById("btn-cancelar-form-actualizar");

    if (btnCancelarFormActualizar) {
        btnCancelarFormActualizar.addEventListener("click", function () {
            mostrarSeccion("seccion-busqueda-actualizar");
        });
    }
}

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

    tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(cb => {
        cb.addEventListener("change", function () {
            tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(other => {
                if (other !== cb) other.checked = false;
            });
        });
    });
}

function precargarFormularioActualizar(ruta) {
    document.getElementById("act-id-ruta").value            = ruta.id_ruta           ?? "";
    document.getElementById("act-localidad-origen").value   = ruta.localidad_origen  ?? "";
    document.getElementById("act-localidad-destino").value  = ruta.localidad_destino ?? "";
    document.getElementById("act-modalidad").value          = ruta.modalidad         ?? "";
    document.getElementById("act-distancia").value          = ruta.distancia         ?? "";
    document.getElementById("act-peso-soportado").value     = ruta.peso_soportado    ?? "";
}

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

function alerta(titulo, mensaje, tipo) {
    return Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        confirmButtonColor: "#5a1e2d"
    });
}