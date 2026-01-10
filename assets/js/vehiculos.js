// =====================================================
//   CRUD DE VEHÍCULOS
//   Secciones: 
//       1. Registrar
//       2. Consultar
//       3. Actualizar
//       4. Eliminar
//       5. Funciones reutilizables
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
    const btnVolver = document.getElementById("btnVolver");
    if (!btnVolver) return;

    btnVolver.addEventListener("click", () => {
        document.getElementById("tablaResultados").style.display = "none";
        document.getElementById("formContainer").style.display = "block";
    });
}

function consultarVehiculos() {
    const formularioConsulta = document.getElementById("formConsultarVehiculos");
    const cuerpoTabla = document.querySelector("#tablaVehiculos tbody");

    if (!formularioConsulta) return;

    formularioConsulta.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const formData = new FormData(formularioConsulta);
        const filtros = Object.fromEntries(formData.entries());

        apiRequest("consultar-vehiculos", filtros)
            .then(res => res.json())
            .then(datos => {
                if (!datos || datos.length === 0) {
                    alerta("Consulta", "No se encontraron vehículos", "warning");
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

                document.getElementById("tablaResultados").style.display = "block";
                document.getElementById("formContainer").style.display = "none";
            })
            .catch(() => alerta("Error", "Error al conectar con el servidor", "error"));
    });
}

/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */
function actualizarVehiculos() {
    const inputBusqueda = document.getElementById('inputBuscarVehiculo');
    const datalist = document.getElementById('listaVehiculos');
    const formulario = document.getElementById('formActualizarVehiculo');

    if (!inputBusqueda) return;

    // Al escribir, cargar placas en el datalist
    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        apiRequest('listar-placas', { busqueda: texto })
            .then(r => r.json())
            .then(datos => {
                datalist.innerHTML = '';
                datos.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.placas;
                    datalist.appendChild(opt);
                });
            });
    });

    // Al seleccionar una placa, traer datos del vehículo
    inputBusqueda.addEventListener('change', () => {
        const placa = inputBusqueda.value;
        apiRequest('obtener-vehiculo-unico', { campo: 'placas', valor: placa })
            .then(r => r.json())
            .then(v => {
                if (!v.id_vehiculo) return;
                
                document.getElementById('inputIdVehiculo').value = v.id_vehiculo;
                document.getElementById('inputIdVehiculoDisplay').value = v.id_vehiculo;
                document.getElementById('inputPlacas').value = v.placas;
                document.getElementById('inputMarca').value = v.marca;
                document.getElementById('inputModelo').value = v.modelo;
                document.getElementById('inputCapacidad').value = v.capacidad_carga;
                document.getElementById('selectTipoVehiculo').value = v.tipo_vehiculo;
                document.getElementById('selectCarroceria').value = v.id_carroceria;

                document.getElementById('contenedorBotones').style.display = 'block';
            });
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
    const select = document.getElementById('filtroBusqueda');
    if (!select) return;

    select.addEventListener('change', function () {
        document.getElementById('campoId').style.display = (this.value === 'id') ? 'block' : 'none';
        document.getElementById('campoPlacas').style.display = (this.value === 'placas') ? 'block' : 'none';
    });
}

function eliminarVehiculos() {
    const formulario = document.getElementById('formConsultaEliminar');
    if (!formulario) return;

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        const filtro = document.getElementById('filtroBusqueda').value;
        const valor = (filtro === 'id') ? document.getElementById('inputId').value : document.getElementById('inputPlacas').value;

        apiRequest("obtener-vehiculo-unico", { campo: filtro, valor: valor })
            .then(res => res.json())
            .then(v => {
                if (!v || !v.id_vehiculo) {
                    alerta("No encontrado", "El vehículo no existe", "warning");
                    return;
                }

                document.getElementById("res_id").value = v.id_vehiculo;
                document.getElementById("res_placas").value = v.placas;
                document.getElementById("res_marca").value = v.marca;
                document.getElementById("res_modelo").value = v.modelo;
                document.getElementById("res_tipo").value = v.tipo_vehiculo;
                document.getElementById("res_capacidad").value = v.capacidad_carga;

                document.getElementById("filtroEliminar").style.display = "none";
                document.getElementById("resultadosBusqueda").style.display = "block";
            });
    });

    const btnEliminar = document.getElementById("btnEliminar");
    if (btnEliminar) {
        btnEliminar.onclick = () => {
            const id = document.getElementById("res_id").value;
            confirmar("¿Eliminar vehículo?", "Esta acción no se puede deshacer", "warning")
                .then(res => {
                    if (res.isConfirmed) {
                        apiRequest("eliminar-vehiculo", { id_vehiculo: id })
                            .then(r => r.text())
                            .then(resp => manejarRespuestaCRUD(resp, "Vehículo eliminado.", "dashboard.php"));
                    }
                });
        };
    }
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