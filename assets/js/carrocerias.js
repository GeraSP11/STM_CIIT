// =====================================================
//  CRUD DE CARROCERÍAS (Módulo 6.1)
//  Basado en la estructura de Localidades CIIT-TMS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // ---- 1. Registrar ----
    configurarRegistroCarroceria();
    gestionarCamposCondicionales(); // Lógica para mostrar/ocultar ejes y contenedores

    // ---- 2. Consultar ----
    configurarVistaConsultarCarrocerias();
    consultarCarrocerias();

    // ---- 3. Actualizar ----
    actualizarCarrocerias();

    // ---- 4. Eliminar ----
    configurarVistaEliminarCarrocerias();
    eliminarCarrocerias();
});

/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */

/**
 * Muestra u oculta campos de Ejes y Contenedores según Modalidad y Tipo.
 */
function gestionarCamposCondicionales() {
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const selectTipo = document.getElementById("tipo_carroceria");
    const divEjes = document.getElementById("campo_ejes"); // ID del contenedor en tu HTML
    const divContenedores = document.getElementById("campo_contenedores");

    if (!selectModalidad || !selectTipo) return;

    const actualizarVisibilidad = () => {
        // Ejes: Carretero o Ferroviario
        const requiereEjes = ["Carretero", "Ferroviario"].includes(selectModalidad.value);
        divEjes.style.display = requiereEjes ? "block" : "none";
        document.getElementById("numero_ejes_vehiculares").required = requiereEjes;

        // Contenedores: Unidad de Carga o Mixta
        const requiereContenedores = ["Unidad de carga", "Mixta"].includes(selectTipo.value);
        divContenedores.style.display = requiereContenedores ? "block" : "none";
        document.getElementById("numero_contenedores").required = requiereContenedores;
    };

    selectModalidad.addEventListener("change", actualizarVisibilidad);
    selectTipo.addEventListener("change", actualizarVisibilidad);
}

function configurarRegistroCarroceria() {
    const formulario = document.querySelector("#formCarrocerias");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Carrocería?", "Se validará la matrícula según la modalidad seleccionada.")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("registrar-carroceria", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Carrocería registrada y validada correctamente.",
                        "consultar-carrocerias.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un problema al conectar con el servidor.", "error"));
            });
    });
}

/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */
function configurarVistaConsultarCarrocerias() {
    const selectFiltros = document.getElementById("selectFiltro");
    const botonAgregarFiltro = document.getElementById("btnAddFiltro");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");

    if (!selectFiltros || !botonAgregarFiltro || !contenedorFiltros) return;

    botonAgregarFiltro.addEventListener("click", () => {
        const valorFiltro = selectFiltros.value;
        const textoFiltro = selectFiltros.options[selectFiltros.selectedIndex].text;

        if (!valorFiltro) {
            alerta("Filtros", "Selecciona un criterio de búsqueda", "warning");
            return;
        }

        const fila = document.createElement("div");
        fila.classList.add("filter-row");
        
        // Adaptamos el input según el filtro (algunos podrían ser select, pero aquí usamos texto por simplicidad del ejemplo)
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Valor a buscar..." required style="width: 40%;">
            <button class="delete-btn"><i class="fas fa-trash"></i></button>
        `;
        contenedorFiltros.appendChild(fila);

        contenedorBotonConsultar.style.display = "flex";
        selectFiltros.querySelector(`option[value="${valorFiltro}"]`).disabled = true;
        selectFiltros.selectedIndex = 0;

        fila.querySelector(".delete-btn").addEventListener("click", () => {
            fila.remove();
            contenedorBotonConsultar.style.display = contenedorFiltros.children.length ? "flex" : "none";
            selectFiltros.querySelector(`option[value="${valorFiltro}"]`).disabled = false;
        });
    });
}

function consultarCarrocerias() {
    const formularioConsulta = document.getElementById("formConsultarCarrocerias");
    const contenedorResultados = document.getElementById("tablaResultados");
    const cuerpoTabla = document.querySelector("#tablaCarrocerias tbody");

    if (!formularioConsulta) return;

    formularioConsulta.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const formData = new FormData(formularioConsulta);
        const filtros = {};
        formData.forEach((value, key) => { if(key !== 'action') filtros[key] = value; });

        apiRequest("consultar-carrocerias", filtros)
            .then(res => res.json())
            .then(datos => {
                if (!datos || datos.length === 0) {
                    alerta("Sin resultados", "No hay carrocerías con esos filtros.", "info");
                    return;
                }

                cuerpoTabla.innerHTML = "";
                datos.forEach(item => {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${item.matricula}</td>
                        <td>${item.modalidad}</td>
                        <td>${item.tipo}</td>
                        <td>${item.estatus}</td>
                        <td>${item.localidad}</td>
                        <td><span class="badge ${item.estatus === 'Disponible' ? 'bg-success' : 'bg-warning'}">${item.estatus}</span></td>
                    `;
                    cuerpoTabla.appendChild(fila);
                });

                contenedorResultados.style.display = "block";
                formularioConsulta.parentElement.style.display = "none";
            })
            .catch(() => alerta("Error", "Error al procesar la consulta.", "error"));
    });

    document.getElementById("btnVolver")?.addEventListener("click", () => {
        contenedorResultados.style.display = "none";
        formularioConsulta.parentElement.style.display = "block";
    });
}

/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */
function actualizarCarrocerias() {
    const inputBusqueda = document.getElementById('inputBuscarCarroceria');
    const datalist = document.getElementById('carroceriasList');
    const formulario = document.getElementById('formActualizarCarroceria');

    if (!inputBusqueda) return;

    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        apiRequest('buscar-carrocerias', { busqueda: texto })
            .then(r => r.json())
            .then(data => {
                datalist.innerHTML = '';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.matricula;
                    // Guardamos todo en dataset para llenar el form rápido
                    Object.keys(c).forEach(key => opt.dataset[key] = c[key]);
                    datalist.appendChild(opt);
                });
            });
    });

    inputBusqueda.addEventListener('change', () => {
        const selected = Array.from(datalist.options).find(o => o.value === inputBusqueda.value);
        if (!selected) return;

        // Llenar campos (Importante: modalidad_carroceria debe ser readonly en tu HTML)
        document.getElementById('id_carroceria').value = selected.dataset.id_carroceria;
        document.getElementById('matricula').value = selected.dataset.matricula;
        document.getElementById('modalidad_display').value = selected.dataset.modalidad_carroceria;
        document.getElementById('peso_vehicular').value = selected.dataset.peso_vehicular;
        // ... llenar el resto de campos (ejes, contenedores, responsable, etc.)

        document.getElementById('contenedorBusqueda').classList.add('oculto');
        document.getElementById('contenedorBotones').style.display = 'block';
    });

    formulario?.addEventListener('submit', e => {
        e.preventDefault();
        confirmar("¿Actualizar Carrocería?", "Se verificará la integridad de los datos.")
            .then(r => {
                if (r.isConfirmed) {
                    apiRequest("actualizar-carroceria", formulario)
                        .then(r => r.text())
                        .then(resp => manejarRespuestaCRUD(resp, "Actualización exitosa.", "consultar-carrocerias.php"));
                }
            });
    });
}

/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */
function eliminarCarrocerias() {
    const formulario = document.getElementById('formConsultaEliminar');
    if (!formulario) return;

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricula = document.getElementById('inputMatriculaEliminar').value;

        apiRequest("mostrar-eliminar-carroceria", { matricula: matricula })
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    alerta("No encontrado", "No existe esa carrocería.", "warning");
                    return;
                }

                const carro = data[0];
                
                // --- REGLA DE SEGURIDAD (DIAGRAMA 6.1.4) ---
                if (carro.estatus_carroceria === "Ensamblada") {
                    alerta("Acción Bloqueada", "No se puede eliminar una carrocería que está 'Ensamblada' en un vehículo.", "error");
                    return;
                }

                confirmar(
                    "¿Eliminar Carrocería?",
                    `¿Está seguro de eliminar la matrícula ${carro.matricula}? Esta acción es irreversible.`,
                    "warning"
                ).then(res => {
                    if (res.isConfirmed) {
                        apiRequest("eliminar-carroceria", { id_carroceria: carro.id_carroceria })
                            .then(r => r.text())
                            .then(resp => manejarRespuestaCRUD(resp, "Eliminación exitosa."));
                    }
                });
            });
    });
}

/* =====================================================
   5. FUNCIONES REUTILIZABLES
   ===================================================== */
function apiRequest(accion, datos = null) {
    const formData = datos instanceof HTMLFormElement ? new FormData(datos) : new FormData();
    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) formData.append(clave, datos[clave]);
    }
    formData.append("action", accion);

    // Cambiamos el endpoint a tu nuevo controlador de carrocerías
    return fetch('/ajax/carroceria-ajax.php', {
        method: "POST",
        body: formData
    });
}

function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {
    if (respuesta.trim() === "OK") {
        alerta("Éxito", mensajeExito, "success").then(() => {
            if (redireccion) window.location.href = redireccion;
            else location.reload();
        });
    } else {
        alerta("Error", respuesta, "error");
    }
}