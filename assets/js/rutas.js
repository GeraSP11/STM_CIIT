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
    eliminarRutas();
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
    _initBtnBuscar();
    _initBtnActualizar();
    _initFormActualizar();
    _initBtnCancelarForm();
    _initSelectModalidad();
    _initBtnCancelarBusqueda();
}

// ── Inicializadores ────────────────────────────────────────────────────────────

function _initBtnBuscar() {
    const btn   = document.getElementById("btn-filtro-actualizar");
    const input = document.getElementById("input-id-ruta-actualizar");
    if (!btn) return;

    btn.addEventListener("click", () => {
        apiRequest("buscar_rutas", { id_ruta: input.value.trim() })
            .then(res => res.json())
            .then(data => renderizarResultadosActualizar(data))
            .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
    });
}

function _initBtnActualizar() {
    const btn = document.getElementById("btn-actualizar");
    if (!btn) return;

    btn.addEventListener("click", () => {
        const seleccionado = document.querySelector(".checkbox-ruta-actualizar:checked");

        if (!seleccionado) {
            alerta("Advertencia", "Debe seleccionar una ruta para actualizar.", "warning");
            return;
        }

        apiRequest("obtener_ruta", { id_ruta: seleccionado.value })
            .then(res => res.json())
            .then(ruta => cargarLocalidadesYPrecargar(ruta))
            .catch(() => alerta("Error", "No se pudieron cargar los datos de la ruta.", "error"));
    });
}

function _initFormActualizar() {
    const form = document.getElementById("form-actualizar-ruta");
    if (!form) return;

    form.addEventListener("submit", e => {
        e.preventDefault();
        if (!validarFormularioActualizar()) return;

        apiRequest("actualizar_ruta", form)
            .then(res => res.text())
            .then(respuesta => {
                if (respuesta.trim() === "OK") {
                    alerta("Éxito", "La ruta fue actualizada correctamente.", "success")
                        .then(() => mostrarSeccionActualizar("seccion-busqueda-actualizar"));
                } else {
                    alerta("Error", respuesta, "error");
                }
            })
            .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
    });
}

function _initBtnCancelarForm() {
    const btn = document.getElementById("btn-cancelar-form-actualizar");
    if (!btn) return;

    btn.addEventListener("click", () => mostrarSeccionActualizar("seccion-busqueda-actualizar"));
}

function _initSelectModalidad() {
    const select = document.getElementById("act-modalidad");
    if (!select) return;

    select.addEventListener("change", () => actualizarCamposModalidad(select.value));
}

function _initBtnCancelarBusqueda() {
    const btn = document.getElementById("btn-cancelar-busqueda");
    if (!btn) return;

    btn.addEventListener("click", () => ocultarResultados());
}

// ── Lógica de campos dinámicos ─────────────────────────────────────────────────

function actualizarCamposModalidad(modalidad) {
    document.querySelectorAll(".campo-modal").forEach(el => {
        const modalidades = el.dataset.modalidad?.split(" ") ?? [];
        el.classList.toggle("d-none", !modalidades.includes(modalidad));
    });
}

// ── Carga de datos ─────────────────────────────────────────────────────────────

function cargarLocalidadesYPrecargar(ruta) {
    apiRequest("obtener_localidades")
        .then(res => res.json())
        .then(localidades => {
            _poblarSelectsLocalidad(localidades);
            precargarFormularioActualizar(ruta);
            mostrarSeccionActualizar("seccion-form-actualizar");
        })
        .catch(() => alerta("Error", "No se pudieron cargar las localidades.", "error"));
}

function _poblarSelectsLocalidad(localidades) {
    const selects = ["act-localidad-origen", "act-localidad-destino"]
        .map(id => document.getElementById(id))
        .filter(Boolean);

    selects.forEach(sel => {
        sel.innerHTML = `<option value="">Seleccione...</option>`;
        localidades.forEach(loc => {
            const opt = document.createElement("option");
            opt.value       = loc.id_localidad;
            opt.textContent = `${loc.nombre_centro_trabajo} — ${loc.localidad}, ${loc.estado}`;
            sel.appendChild(opt);
        });
    });
}

function precargarFormularioActualizar(ruta) {
    const campos = {
        "act-id-ruta"           : ruta.id_ruta,
        "act-localidad-origen"  : ruta.localidad_origen,
        "act-localidad-destino" : ruta.localidad_destino,
        "act-modalidad"         : ruta.modalidad,
        "act-tipo-ruta"         : ruta.tipo_ruta,
        "act-distancia"         : ruta.distancia,
        "act-peso-soportado"    : ruta.peso_soportado,
        "act-teus"              : ruta.teus,
        "act-carga-max"         : ruta.carga_max,
        "act-descripcion"       : ruta.descripcion,
    };

    Object.entries(campos).forEach(([id, valor]) => {
        const el = document.getElementById(id);
        if (el) el.value = valor ?? "";
    });

    // Dispara cambio de modalidad para mostrar campos condicionales
    actualizarCamposModalidad(ruta.modalidad ?? "");
}

// ── Secciones ──────────────────────────────────────────────────────────────────

function mostrarSeccionActualizar(idSeccion) {
    ["seccion-busqueda-actualizar", "seccion-form-actualizar"].forEach(id => {
        document.getElementById(id)?.classList.toggle("d-none", id !== idSeccion);
    });

    if (idSeccion === "seccion-busqueda-actualizar") ocultarResultados();

    window.scrollTo({ top: 0, behavior: "smooth" });
}

// ── Tabla de resultados ────────────────────────────────────────────────────────

function renderizarResultadosActualizar(rutas) {
    const tabla = document.getElementById("tabla-resultados-actualizar");
    const tbody = tabla?.querySelector("tbody");
    if (!tbody) return;

    tbody.innerHTML = rutas?.length
        ? rutas.map(ruta => `
            <tr>
                <td><input type="checkbox" class="checkbox-ruta-actualizar" value="${ruta.id_ruta}"></td>
                <td>${ruta.id_ruta}</td>
                <td>${ruta.modalidad}</td>
            </tr>`).join("")
        : `<tr><td colspan="3" class="text-center text-muted">No se encontraron resultados.</td></tr>`;

    tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(cb => {
        cb.addEventListener("change", () => _desmarcarOtros(cb, tbody));
    });

    document.getElementById("label-resultados")?.classList.remove("d-none");
    tabla.classList.remove("d-none");
    document.getElementById("acciones-busqueda")?.classList.remove("d-none");
}

function _desmarcarOtros(cbActivo, tbody) {
    tbody.querySelectorAll(".checkbox-ruta-actualizar").forEach(cb => {
        if (cb !== cbActivo) cb.checked = false;
    });
}

function ocultarResultados() {
    ["label-resultados", "tabla-resultados-actualizar", "acciones-busqueda"]
        .forEach(id => document.getElementById(id)?.classList.add("d-none"));

    const tbody = document.querySelector("#tabla-resultados-actualizar tbody");
    if (tbody) tbody.innerHTML = "";

    const input = document.getElementById("input-id-ruta-actualizar");
    if (input) input.value = "";
}

// ── Validación ─────────────────────────────────────────────────────────────────

function validarFormularioActualizar() {
    const valores = _obtenerValoresFormulario();
    return _validarCamposBase(valores) && _validarPorModalidad(valores);
}

function _obtenerValoresFormulario() {
    return {
        idRuta         : document.getElementById("act-id-ruta").value.trim(),
        localidadOrigen: document.getElementById("act-localidad-origen").value.trim(),
        localidadDestino:document.getElementById("act-localidad-destino").value.trim(),
        modalidad      : document.getElementById("act-modalidad").value.trim(),
        tipoRuta       : document.getElementById("act-tipo-ruta").value.trim(),
        distancia      : document.getElementById("act-distancia").value.trim(),
        pesoSoportado  : document.getElementById("act-peso-soportado").value.trim(),
        teus           : document.getElementById("act-teus").value.trim(),
        cargaMax       : document.getElementById("act-carga-max").value.trim(),
    };
}

function _validarCamposBase({ idRuta, localidadOrigen, localidadDestino, modalidad }) {
    if (!idRuta) {
        return _error("El identificador de ruta es requerido.");
    }
    if (idRuta.length > 20) {
        return _error("El ID de ruta no puede exceder 20 caracteres.");
    }
    if (!localidadOrigen || !localidadDestino) {
        return _error("Las localidades de origen y destino son requeridas.");
    }
    if (localidadOrigen === localidadDestino) {
        return _error("La localidad de origen y destino no pueden ser la misma.");
    }
    if (!modalidad) {
        return _error("La modalidad es requerida.");
    }
    return true;
}

function _validarPorModalidad(valores) {
    const validadores = {
        "Carretera"  : _validarCarretera,
        "Ferroviaria": _validarFerroviaria,
        "Marítima"   : _validarMaritima,
        "Aérea"      : _validarAerea,
    };

    return validadores[valores.modalidad]?.(valores) ?? true;
}

function _validarCarretera({ tipoRuta, distancia, pesoSoportado }) {
    if (!tipoRuta) {
        return _error("El tipo de ruta es requerido para modalidad Carretera.");
    }
    if (!_esPositivo(distancia)) {
        return _error("La distancia es requerida y debe ser un valor positivo.");
    }
    if (pesoSoportado !== "") {
        if (!_esPositivo(pesoSoportado)) {
            return _error("El peso soportado debe ser un valor positivo.");
        }
        if (tipoRuta === "B" && Number(pesoSoportado) > 38) {
            return _error("Para tipo B el peso soportado no puede exceder 38 toneladas.");
        }
        if (tipoRuta === "C" && Number(pesoSoportado) > 25.5) {
            return _error("Para tipo C el peso soportado no puede exceder 25.5 toneladas.");
        }
    }
    return true;
}

function _validarFerroviaria({ distancia, pesoSoportado }) {
    if (!_esPositivo(distancia)) {
        return _error("La distancia es requerida y debe ser un valor positivo.");
    }
    if (pesoSoportado !== "" && !_esPositivo(pesoSoportado)) {
        return _error("El peso soportado debe ser un valor positivo.");
    }
    return true;
}

function _validarMaritima({ distancia, pesoSoportado, teus }) {
    if (!_esPositivo(distancia)) {
        return _error("La distancia es requerida y debe ser un valor positivo.");
    }
    if (pesoSoportado !== "" && !_esPositivo(pesoSoportado)) {
        return _error("El peso soportado debe ser un valor positivo.");
    }
    if (teus !== "" && (!_esPositivo(teus) || !Number.isInteger(Number(teus)))) {
        return _error("La capacidad en TEUs debe ser un número entero positivo.");
    }
    return true;
}

function _validarAerea({ distancia, cargaMax }) {
    if (!_esPositivo(distancia)) {
        return _error("La distancia es requerida y debe ser un valor positivo.");
    }
    if (cargaMax !== "" && !_esPositivo(cargaMax)) {
        return _error("La carga máxima debe ser un valor positivo en kg.");
    }
    return true;
}

// ── Helpers ────────────────────────────────────────────────────────────────────

function _esPositivo(valor) {
    return valor !== "" && !isNaN(Number(valor)) && Number(valor) > 0;
}

function _error(mensaje) {
    alerta("Error", mensaje, "error");
    return false;
}


/* =====================================================
   4. Eliminar Ruta
   ===================================================== */

function eliminarRutas() {

    const btnBuscar   = document.getElementById("btn-buscar-eliminar");
    const btnEliminar = document.getElementById("btn-confirmar-eliminar");
    const btnCancelar = document.getElementById("btn-cancelar-eliminar");
    const checkTodos  = document.getElementById("check-todos");

    if (!btnBuscar) return;

    // ── 4.1 Buscar rutas ─────────────────────────────────────────────
    btnBuscar.addEventListener("click", function () {
        const idRuta = document.getElementById("input-id-ruta-eliminar").value.trim();

        apiRequest("buscar_rutas", { id_ruta: idRuta })
            .then(res => res.json())
            .then(data => renderizarResultadosEliminar(data))
            .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
    });

    // ── 4.2 Seleccionar / deseleccionar todos ────────────────────────
    if (checkTodos) {
        checkTodos.addEventListener("change", function () {
            document.querySelectorAll(".checkbox-ruta-eliminar")
                .forEach(cb => cb.checked = this.checked);
        });
    }

    // ── 4.3 Confirmar eliminación ────────────────────────────────────
    if (btnEliminar) {
        btnEliminar.addEventListener("click", function () {
            const seleccionados = [
                ...document.querySelectorAll(".checkbox-ruta-eliminar:checked")
            ].map(cb => cb.value);

            if (seleccionados.length === 0) {
                alerta("Advertencia", "Debe seleccionar al menos una ruta para eliminar.", "warning");
                return;
            }

            const cantidad = seleccionados.length;
            const mensaje  = cantidad === 1
                ? "¿Está seguro de eliminar la ruta seleccionada? Esta acción no se puede deshacer."
                : `¿Está seguro de eliminar las ${cantidad} rutas seleccionadas? Esta acción no se puede deshacer.`;

            Swal.fire({
                title: "Confirmar eliminación",
                text: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#5a1e2d",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (!result.isConfirmed) return;

                apiRequest("eliminar_rutas", { ids_rutas: seleccionados.join(",") })
                    .then(res => res.text())
                    .then(respuesta => {
                        if (respuesta.trim() === "OK") {
                            alerta("Éxito", "Ruta(s) eliminada(s) correctamente.", "success")
                                .then(() => ocultarResultadosEliminar());
                        } else {
                            alerta("Error", respuesta, "error");
                        }
                    })
                    .catch(() => alerta("Error", "No se pudo conectar con el servidor.", "error"));
            });
        });
    }

    // ── 4.4 Cancelar ─────────────────────────────────────────────────
    if (btnCancelar) {
        btnCancelar.addEventListener("click", ocultarResultadosEliminar);
    }
}

// ── Renderizar tabla de resultados para eliminar ───────────────────────────────
function renderizarResultadosEliminar(rutas) {
    const tbody  = document.getElementById("tbody-eliminar");
    const tabla  = document.getElementById("tabla-resultados-eliminar");
    const check  = document.getElementById("check-todos");
    if (!tbody) return;

    tbody.innerHTML = "";
    if (check) check.checked = false;

    if (!rutas || rutas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-3">
                    No se encontraron rutas con ese filtro.
                </td>
            </tr>`;
    } else {
        rutas.forEach(ruta => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="checkbox-ruta-eliminar" value="${ruta.id_ruta}">
                </td>
                <td>${ruta.id_ruta}</td>
                <td>${ruta.nombre_origen  ?? "—"}</td>
                <td>${ruta.nombre_destino ?? "—"}</td>
                <td>${ruta.modalidad_ruta ?? ruta.modalidad ?? "—"}</td>
                <td>${ruta.tipo_ruta      ?? "—"}</td>
                <td>${ruta.distancia      ?? "—"}</td>
            `;

            // Clic en fila marca el checkbox
            tr.addEventListener("click", function (e) {
                if (e.target.type === "checkbox") return;
                const cb = tr.querySelector(".checkbox-ruta-eliminar");
                cb.checked = !cb.checked;
                sincronizarCheckTodos();
            });

            // Clic en checkbox sincroniza el "seleccionar todos"
            tr.querySelector(".checkbox-ruta-eliminar")
                .addEventListener("change", sincronizarCheckTodos);

            tbody.appendChild(tr);
        });
    }

    document.getElementById("label-resultados-eliminar")?.classList.remove("d-none");
    tabla?.classList.remove("d-none");
    document.getElementById("acciones-eliminar")?.classList.remove("d-none");
    document.getElementById("resultado-container").style.display = "block";
}

function sincronizarCheckTodos() {
    const todos      = document.querySelectorAll(".checkbox-ruta-eliminar");
    const marcados   = document.querySelectorAll(".checkbox-ruta-eliminar:checked");
    const checkTodos = document.getElementById("check-todos");
    if (checkTodos) checkTodos.checked = todos.length > 0 && todos.length === marcados.length;
}

function ocultarResultadosEliminar() {
    ["label-resultados-eliminar", "tabla-resultados-eliminar", "acciones-eliminar"]
        .forEach(id => document.getElementById(id)?.classList.add("d-none"));

    const tbody = document.getElementById("tbody-eliminar");
    if (tbody) tbody.innerHTML = "";

    const input = document.getElementById("input-id-ruta-eliminar");
    if (input) input.value = "";

    const check = document.getElementById("check-todos");
    if (check) check.checked = false;

    document.getElementById("resultado-container").style.display = "none";
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