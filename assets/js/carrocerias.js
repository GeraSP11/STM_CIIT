// =====================================================
//  CRUD DE CARROCERÍAS (Módulo 6.1)
//  Basado en la estructura de Localidades CIIT-TMS
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    // ---- 0. Carga de Catálogos (NUEVO) ----
    if (document.getElementById('localidad_pertenece')) cargarLocalidades();
    if (document.getElementById('responsable_carroceria')) cargarPersonal();

    // ---- 1. Registrar ----
    configurarRegistroCarroceria();
    gestionarCamposCondicionales(); 

    // ---- 2. Consultar ----
    configurarVistaConsultarCarrocerias();
    consultarCarrocerias();

    // ---- 3. Actualizar ----
    actualizarCarrocerias();
    if (document.getElementById('buscar_matricula')) {
        inicializarActualizador();
    }

    // ---- 4. Eliminar ----
    /* configurarVistaEliminarCarrocerias();
    */eliminarCarrocerias();  
});

/* =====================================================
   0. CARGA DE CATÁLOGOS (LOCALIDADES Y PERSONAL)
   ===================================================== */
function cargarLocalidades() {
    apiRequest("obtener-localidades")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('localidad_pertenece');
            if (!select) return;

            if (data.length === 0) {
                select.innerHTML = '<option value="">Sin localidades registradas</option>';
                select.disabled = true; // Opcional: deshabilitar si no hay datos
            } else {
                select.disabled = false;
                select.innerHTML = '<option value="">Seleccione localidad</option>';
                data.forEach(loc => {
                    select.add(new Option(loc.nombre_display, loc.id_localidad));
                });
            }
        });
}

function cargarPersonal() {
    apiRequest("obtener-personal")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('responsable_carroceria');
            if (!select) return;

            if (data.length === 0) {
                select.innerHTML = '<option value="">Sin personal registrado</option>';
                select.disabled = true;
            } else {
                select.disabled = false;
                select.innerHTML = '<option value="">Seleccione responsable</option>';
                data.forEach(p => {
                    select.add(new Option(p.nombre_completo, p.id_personal));
                });
            }
        });
}

/* =====================================================
   1. REGISTRAR (CREATE) CON VALIDACIONES TÉCNICAS
   ===================================================== */

const ValidadoresMatricula = {
    Carretero: (v) => {
        const regex = /^[A-HJ-NPR-Z0-9]{8}[0-9X][A-Z][A-HJ-NPR-Z0-9]{7}$/;
        return regex.test(v);
    },
    Ferroviario: (v) => {
        if (!/^\d{12}$/.test(v)) return false;
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            let n = parseInt(v[i]);
            if (i % 2 === 0) {
                n *= 2;
                if (n > 9) n -= 9;
            }
            sum += n;
        }
        return sum % 10 === 0;
    },
    Maritimo: (v) => {
        if (!/^\d{7}$/.test(v)) return false;
        let checkSum = 0;
        let pesos = [7, 6, 5, 4, 3, 2];
        for (let i = 0; i < 6; i++) {
            checkSum += parseInt(v[i]) * pesos[i];
        }
        return (checkSum % 10) === parseInt(v[6]);
    },
    Aereo: (v) => {
        return /^[A-Z]{1,2}[A-Z0-9]{1,5}$/.test(v);
    }
};

function gestionarCamposCondicionales() {
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const selectTipo = document.getElementById("tipo_carroceria");
    const inputEjes = document.getElementById("numero_ejes_vehiculares");
    const inputContenedores = document.getElementById("numero_contenedores");

    if (!selectModalidad || !selectTipo) return;

    const actualizarVisibilidad = () => {
        const mod = selectModalidad.value;
        if (["Marítimo", "Aéreo"].includes(mod)) {
            selectTipo.value = "Mixta";
            selectTipo.disabled = true;
            inputEjes.disabled = true;
            inputEjes.required = false;
        } else {
            selectTipo.disabled = false;
            inputEjes.disabled = false;
            inputEjes.required = true;
        }

        const requiereContenedores = ["Unidad de carga", "Mixta"].includes(selectTipo.value);
        inputContenedores.disabled = !requiereContenedores;
        inputContenedores.required = requiereContenedores;
    };

    selectModalidad.addEventListener("change", actualizarVisibilidad);
    selectTipo.addEventListener("change", actualizarVisibilidad);
}

function configurarRegistroCarroceria() {
    const formPrincipal = document.querySelector("#formCarrocerias");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const seccionDetalles = document.getElementById("seccionDetalles"); 

    if (!formPrincipal) return;

    formPrincipal.addEventListener("input", () => {
        const mod = document.getElementById("modalidad_carroceria").value;
        const mat = document.getElementById("matricula").value;
        const peso = parseFloat(document.getElementById("peso_vehicular").value);
        
        const matValida = ValidadoresMatricula[mod] ? ValidadoresMatricula[mod](mat) : false;
        const pesoValido = peso > 0; 

        btnSiguiente.disabled = !(matValida && pesoValido && formPrincipal.checkValidity());
    });

    btnSiguiente?.addEventListener("click", () => {
        const tipo = document.getElementById("tipo_carroceria").value;
        if (tipo === "Unidad de arrastre") {
            ejecutarRegistroFinal(formPrincipal);
        } else {
            generarFormularioDetalles();
            document.getElementById("seccionPrincipal").style.display = "none";
            seccionDetalles.style.display = "block";
        }
    });
}

function generarFormularioDetalles() {
    const numContenedores = document.getElementById('numero_contenedores').value;
    const contenedor = document.getElementById('contenedor-detalles'); // El ID corregido
    
    contenedor.innerHTML = ''; // Limpiar contenido previo

    for (let i = 1; i <= numContenedores; i++) {
        contenedor.innerHTML += `
            <div class="card-detalle shadow-sm mb-3">
                <h5>Contenedor #${i}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label>Longitud (m)</label>
                        <input type="number" name="longitud[]" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label>Anchura (m)</label>
                        <input type="number" name="anchura[]" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label>Altura (m)</label>
                        <input type="number" name="altura[]" class="form-control" step="0.01" required>
                    </div>
                </div>
            </div>`;
    }
}

function ejecutarRegistroFinal(form) {
    confirmar("¿Guardar Registro?", "Se enviarán los datos a la base de datos.")
        .then(r => {
            if (!r.isConfirmed) return;
            apiRequest("registrar-carroceria", form)
                .then(r => r.text())
                .then(resp => manejarRespuestaCRUD(resp, "Registro exitoso", "consultar-carrocerias.php"));
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
        fila.classList.add("filter-row", "mb-2", "d-flex", "gap-2");
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Valor a buscar..." required style="width: 40%;">
            <button class="btn btn-danger delete-btn"><i class="fas fa-trash"></i></button>
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
                    cuerpoTabla.innerHTML += `
                        <tr>
                            <td>${item.matricula}</td>
                            <td>${item.modalidad}</td>
                            <td>${item.tipo}</td>
                            <td>${item.localidad}</td>
                            <td><span class="badge ${item.estatus === 'Disponible' ? 'bg-success' : 'bg-warning'}">${item.estatus}</span></td>
                        </tr>`;
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
                    Object.keys(c).forEach(key => opt.dataset[key] = c[key]);
                    datalist.appendChild(opt);
                });
            });
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

function inicializarActualizador() {
    const inputBusqueda = document.getElementById('buscar_matricula');
    const btnCargar = document.getElementById('btnCargarCarroceria');
    const datalist = document.getElementById('lista_carrocerias');

    inputBusqueda.addEventListener('input', function() {
        const texto = this.value;
        if (texto.length > 2) {
            apiRequest('buscar-carrocerias', { busqueda: texto })
            .then(res => res.json())
            .then(data => {
                datalist.innerHTML = '';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.matricula;
                    datalist.appendChild(option);
                });
            });
        }
    });

    btnCargar.addEventListener('click', function() {
        const matricula = inputBusqueda.value;
        if (!matricula) return alerta("Atención", "Ingrese una matrícula", "warning");

        apiRequest('mostrar-eliminar-carroceria', { matricula: matricula })
        .then(res => res.json())
        .then(res => {
            if (res && res.length > 0) {
                const c = res[0];
                document.getElementById('id_carroceria').value = c.id_carroceria;
                document.getElementById('matricula').value = c.matricula;
                document.getElementById('modalidad_carroceria').value = c.modalidad_carroceria;
                document.getElementById('estatus_carroceria').value = c.estatus_carroceria;
                document.getElementById('peso_vehicular').value = c.peso_vehicular;
                document.getElementById('numero_ejes_vehiculares').value = c.numero_ejes_vehiculares;
                document.getElementById('tipo_carroceria').value = c.tipo_carroceria;
                document.getElementById('localidad_pertenece').value = c.id_localidad; // Setea el select
                document.getElementById('responsable_carroceria').value = c.id_personal; // Setea el select
                
                document.getElementById('btnActualizar').disabled = false;
                alerta("Éxito", "Datos cargados correctamente", "success");
            } else {
                alerta("No encontrado", "No existe esa matrícula", "error");
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
                if (carro.estatus_carroceria === "Ensamblada") {
                    alerta("Acción Bloqueada", "No se puede eliminar una carrocería ensamblada.", "error");
                    return;
                }

                confirmar("¿Eliminar?", `¿Eliminar matrícula ${carro.matricula}?`, "warning")
                .then(res => {
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
   5. FUNCIONES REUTILIZABLES (ESTÁNDAR)
   ===================================================== */
function apiRequest(accion, datos = null) {
    const formData = datos instanceof HTMLFormElement ? new FormData(datos) : new FormData();
    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) formData.append(clave, datos[clave]);
    }
    formData.append("action", accion);

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