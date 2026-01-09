// =====================================================
//  CRUD DE VEHÍCULOS
//  Secciones: 
//      1. Registrar
//      2. Consultar
//      3. Actualizar
//      4. Eliminar
//      5. Funciones reutilizables
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // ---- 1. Registrar ----
    configurarRegistroVehiculos();

    // ---- 2. Consultar ----
    configurarVistaConsultarVehiculos();
    consultarVehiculos();

    // ---- 3. Actualizar ----
    actualizarVehiculos();

    // ---- 4. Eliminar ----
    configurarVistaEliminarVehiculos();
    eliminarVehiculos();
});

/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */
function configurarRegistroVehiculos() {
    const formulario = document.querySelector("#formVehiculos");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        confirmar("¿Registrar Vehículo?", "¿Deseas dar de alta esta unidad?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequest("registrar-vehiculo", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(
                        resp,
                        "Vehículo registrado correctamente.",
                        "dashboard.php"
                    ))
                    .catch(() => alerta("Error", "Ocurrió un problema en la petición.", "error"));
            });
    });
}

/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */
function configurarVistaConsultarVehiculos() {
    const selectFiltros = document.getElementById("selectFiltroVehiculo");
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
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Ingrese ${textoFiltro}" required style="width: 40%;">
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

function consultarVehiculos() {
    const formularioConsulta = document.getElementById("formConsultarVehiculos");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorResultados = document.getElementById("tablaResultadosVehiculos");
    const cuerpoTabla = document.querySelector("#tablaVehiculos tbody");
    const botonVolver = document.getElementById("btnVolver");

    if (!formularioConsulta) return;

    formularioConsulta.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const filtros = {};
        // Captura dinámica de filtros (placas, modelo, marca, etc)
        const inputs = contenedorFiltros.querySelectorAll('input[name]');
        inputs.forEach(input => {
            if (input.value.trim()) filtros[input.name] = input.value.trim();
        });

        apiRequest("consultar-vehiculos", filtros)
            .then(res => res.json())
            .then(datos => {
                if (!datos || datos.length === 0) {
                    alerta("Consulta", "No se encontraron vehículos con esos criterios", "warning");
                    return;
                }

                cuerpoTabla.innerHTML = "";
                datos.forEach(v => {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${v.placas}</td>
                        <td>${v.marca}</td>
                        <td>${v.modelo}</td>
                        <td>${v.capacidad_carga} kg</td>
                        <td>${v.tipo_vehiculo}</td>
                    `;
                    cuerpoTabla.appendChild(fila);
                });

                contenedorResultados.style.display = "block";
                formularioConsulta.parentElement.style.display = "none";
            })
            .catch(() => alerta("Error", "Error al conectar con el servidor", "error"));
    });

    botonVolver.addEventListener("click", () => {
        contenedorResultados.style.display = "none";
        formularioConsulta.parentElement.style.display = "block";
    });
}

/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */
function actualizarVehiculos() {
    const inputBusqueda = document.getElementById('inputBuscarPlaca');
    const datalist = document.getElementById('listaVehiculos');
    const formulario = document.getElementById('formActualizarVehiculo');

    if (!inputBusqueda) return;

    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        apiRequest('buscar-vehiculo-placa', { placas: texto })
            .then(r => r.json())
            .then(vehiculos => {
                datalist.innerHTML = '';
                vehiculos.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.placas;
                    // Guardamos datos en dataset para el autocompletado
                    opt.dataset.id = v.id_vehiculo;
                    opt.dataset.marca = v.marca;
                    opt.dataset.modelo = v.modelo;
                    opt.dataset.capacidad = v.capacidad_carga;
                    datalist.appendChild(opt);
                });
            });
    });

    inputBusqueda.addEventListener('change', () => {
        const seleccion = Array.from(datalist.options).find(o => o.value === inputBusqueda.value);
        if (!seleccion) return;

        // Llenar campos del formulario
        document.getElementById('inputIdVehiculo').value = seleccion.dataset.id;
        document.getElementById('inputMarca').value = seleccion.dataset.marca;
        document.getElementById('inputModelo').value = seleccion.dataset.modelo;
        document.getElementById('inputCapacidad').value = seleccion.dataset.capacidad;

        document.getElementById('contenedorBusqueda').classList.add('oculto');
        document.getElementById('contenedorBotones').style.display = 'block';
    });

    formulario.addEventListener('submit', e => {
        e.preventDefault();
        confirmar("¿Actualizar unidad?", "Se guardarán los cambios en el sistema")
            .then(r => {
                if (!r.isConfirmed) return;
                apiRequest("actualizar-vehiculo", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(resp, "Vehículo actualizado.", "actualizar-vehiculos.php"));
            });
    });
}

/* =====================================================
   4. ELIMINAR (DELETE)
   ===================================================== */
function configurarVistaEliminarVehiculos() {
    const select = document.getElementById('filtroBusquedaVehiculo');
    if (!select) return;

    select.addEventListener('change', function () {
        document.querySelectorAll(".campo-dinamico").forEach(c => c.style.display = "none");
        const target = document.getElementById('campo' + this.value);
        if (target) target.style.display = "block";
    });
}

function eliminarVehiculos() {
    const formulario = document.getElementById('formConsultaEliminar');
    if (!formulario) return;

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        const filtro = document.getElementById('filtroBusquedaVehiculo').value;
        const valor = document.querySelector(`#campo${filtro} input`).value;

        apiRequest("mostrar-eliminar-vehiculo", { [filtro]: valor })
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    alerta("No encontrado", "El vehículo no existe", "warning");
                    return;
                }
                const v = data[0];
                // Llenar vista previa de eliminación
                document.getElementById("res_placas").value = v.placas;
                document.getElementById("resultadosVehiculo").style.display = "block";

                document.getElementById("btnEliminarVehiculo").onclick = () => {
                    confirmar("¿Eliminar vehículo?", "Esta acción puede afectar fletes programados", "warning")
                        .then(res => {
                            if (res.isConfirmed) {
                                apiRequest("eliminar-vehiculo", { id_vehiculo: v.id_vehiculo })
                                    .then(r => r.text())
                                    .then(resp => manejarRespuestaCRUD(resp, "Vehículo eliminado.", "dashboard.php"));
                            }
                        });
                };
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

    // Cambiado a la ruta de vehículos
    return fetch('/ajax/vehiculo-ajax.php', {
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