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
    configurarValidacionMatriculaRealTime();

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

    const form = document.getElementById("formCarrocerias");
    form.addEventListener("submit", function(e) {
        e.preventDefault(); // <--- ESTO evita que la página se salga/recargue
        ejecutarRegistroFinal();
    });
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

/* =====================================================
   0. CARGA DE CATÁLOGOS (PERSONAL FILTRADO)
   ===================================================== */
function cargarPersonal() {
    apiRequest("obtener-personal")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('responsable_carroceria');
            if (!select) return;

            // Limpiamos el select y ponemos la opción por defecto
            select.innerHTML = '<option value="">Seleccione responsable</option>';

            if (!data || data.length === 0) {
                select.innerHTML = '<option value="">Sin Jefes de Almacén registrados</option>';
                select.disabled = true;
            } else {
                select.disabled = false;
                // Simplemente recorremos los datos que envía el PHP 
                // (el PHP ya hizo el trabajo de filtrar por 'Jefe de Almacén')
                data.forEach(p => {
                    select.add(new Option(p.nombre_completo, p.id_personal));
                });
            }
        })
        .catch(err => {
            console.error("Error al cargar personal:", err);
        });
}

/* =====================================================
    1. REGISTRAR (CREATE) CON VALIDACIONES TÉCNICAS
   ===================================================== */

const ValidadoresMatricula = {
    Carretero: (v) => {
        const regex = /^[A-HJ-NPR-Z0-9]{8}[0-9X]{1}[A-HJ-NPR]{1}[A-HJ-NPR-Z0-9]{7}$/;
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
    Marítimo: (v) => {
        if (!/^\d{7}$/.test(v)) return false;
        let checkSum = 0;
        let pesos = [7, 6, 5, 4, 3, 2];
        for (let i = 0; i < 6; i++) {
            checkSum += parseInt(v[i]) * pesos[i];
        }
        return (checkSum % 10) === parseInt(v[6]);
    },
    Aéreo: (v) => {
        return /^[A-Z]{1,2}[A-Z0-9]{1,5}$/.test(v);
    }
};

function configurarValidacionMatriculaRealTime() {
    const inputMatricula = document.getElementById("matricula");
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const msjError = document.getElementById("msj-error-matricula");

    const mensajesAyuda = {
        Carretero: "Formato VIN: 17 caracteres alfanuméricos (sin I, O, Q).",
        Ferroviario: "Deben ser 12 dígitos numéricos exactos.",
        Marítimo: "Deben ser 7 dígitos (el último es verificador).",
        Aéreo: "Prefijo de 1-2 letras seguido de 1-5 caracteres."
    };

    const validarAccion = () => {
        const modalidad = selectModalidad.value;
        const valor = inputMatricula.value.trim();

        // 1. Si está vacío o no hay modalidad, resetear estado
        if (!modalidad || valor === "") {
            inputMatricula.classList.remove("input-valido", "input-invalido");
            msjError.textContent = "";
            return;
        }

        // 2. Ejecutar validador técnico
        const validador = ValidadoresMatricula[modalidad];
        const esValido = validador ? validador(valor) : true;

        if (esValido) {
            // ESTADO VÁLIDO: Usamos tus clases CSS
            inputMatricula.classList.remove("input-invalido");
            inputMatricula.classList.add("input-valido");
            msjError.textContent = ""; // Limpiar mensaje
            inputMatricula.title = "Formato correcto";
        } else {
            // ESTADO INVÁLIDO: Usamos tus clases CSS
            inputMatricula.classList.remove("input-valido");
            inputMatricula.classList.add("input-invalido");
            
            // Mostrar el mensaje de ayuda específico en el <small> que creaste
            msjError.textContent = mensajesAyuda[modalidad] || "Formato no válido";
            
            // Tooltip nativo (al pasar el mouse)
            inputMatricula.title = mensajesAyuda[modalidad];
        }
    };

    // Escuchar cambios en ambos campos
    inputMatricula.addEventListener("input", validarAccion);
    selectModalidad.addEventListener("change", validarAccion);
}

/* =====================================================
   1. GESTIÓN DE CONDICIONALES (RESTRICCIÓN FERROVIARIA)
   ===================================================== */
function gestionarCamposCondicionales() {
    const selectModalidad = document.getElementById("modalidad_carroceria");
    const selectTipo = document.getElementById("tipo_carroceria");
    const inputEjes = document.getElementById("numero_ejes_vehiculares");
    const inputContenedores = document.getElementById("numero_contenedores");

    if (!selectModalidad || !selectTipo) return;

    const actualizarVisibilidad = () => {
        const mod = selectModalidad.value;
        const tipo = selectTipo.value;

        // REQUERIMIENTO: Ferroviario no permite "Mixta"
        if (mod === "Ferroviario" && tipo === "Mixta") {
            alerta("Validación", "La modalidad Ferroviaria no permite el tipo de carrocería Mixta.", "warning");
            selectTipo.value = ""; // Reseteamos la selección
            return;
        }

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

/* =====================================================
   VALIDACIÓN DE ERRORES ESPECÍFICOS
   ===================================================== */
function validarFormularioCompleto() {
    const matricula = document.getElementById("matricula").value.trim();
    const modalidad = document.getElementById("modalidad_carroceria").value;
    const tipo = document.getElementById("tipo_carroceria").value;
    const peso = document.getElementById("peso_vehicular").value;
    const responsable = document.getElementById("responsable_carroceria").value;
    const localidad = document.getElementById("localidad_pertenece").value;

    if (!matricula) return "La Matrícula es obligatoria.";
    if (!modalidad) return "Debe seleccionar una Modalidad.";
    if (!tipo) return "Debe seleccionar el Tipo de Carrocería.";
    if (!peso || peso <= 0) return "El Peso Vehicular debe ser un número mayor a 0.";
    if (!responsable) return "Debe asignar un Responsable (Jefe de Almacén).";
    if (!localidad) return "Debe seleccionar la Localidad a la que pertenece.";
    
    // Validación específica Ferroviario vs Mixto (Doble check)
    if (modalidad === "Ferroviario" && tipo === "Mixta") {
        return "Combinación inválida: Ferroviario no puede ser de tipo Mixta.";
    }

    return null; // Todo correcto
}



/* =====================================================
   1. REGISTRAR (CORREGIDO)
   ===================================================== */

function configurarRegistroCarroceria() {
    const formPrincipal = document.querySelector("#formCarrocerias");
    const btnSiguiente = document.getElementById("btnSiguiente");
    const seccionDetalles = document.getElementById("seccionDetalles"); 

    if (!formPrincipal) return;

    // Validación en tiempo real para habilitar el botón
    formPrincipal.addEventListener("input", () => {
        const mod = document.getElementById("modalidad_carroceria").value;
        const mat = document.getElementById("matricula").value;
        const peso = parseFloat(document.getElementById("peso_vehicular").value);
        
        // Simplificamos la validación de matrícula para que no sea tan estricta 
        // y permita avanzar si hay texto (puedes volver a usar ValidadoresMatricula[mod] si lo prefieres)
        const matValida = mat.trim().length >= 3; 
        
        const pesoValido = !isNaN(peso) && peso > 0;
        const formValido = formPrincipal.checkValidity();

        btnSiguiente.disabled = !(matValida && pesoValido && formValido);
    });

    // Acción del botón Siguiente
    btnSiguiente?.addEventListener("click", () => {
        const tipo = document.getElementById("tipo_carroceria").value;
        
        // ASEGURAMOS QUE LOS CAMPOS SEAN ENVIABLES
        // Usar readOnly garantiza que el valor sea visible y se incluya en el FormData
        const camposParaBloquear = ["matricula", "peso_vehicular", "numero_ejes_vehiculares", "numero_contenedores"];
        
        camposParaBloquear.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.readOnly = true;
                el.classList.add("bg-light"); // Feedback visual de que está bloqueado
            }
        });
        
        // Para los SELECTS (que no tienen readOnly), bloqueamos la interacción visual
        const selects = ["modalidad_carroceria", "localidad_pertenece", "responsable_carroceria", "tipo_carroceria"];
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.pointerEvents = "none";
                el.style.backgroundColor = "#e9ecef"; // Color grisáceo de Bootstrap
            }
        });

        if (tipo === "Unidad de arrastre") {
            ejecutarRegistroFinal();
        } else {
            generarFormularioDetalles();
            document.getElementById("seccionPrincipal").style.display = "none";
            document.getElementById("seccionDetalles").style.display = "block";
        }
    });

    document.getElementById("btnAnterior")?.addEventListener("click", () => {
        // 1. Mostrar sección principal y ocultar detalles
        document.getElementById("seccionPrincipal").style.display = "block";
        document.getElementById("seccionDetalles").style.display = "none";

        // 2. DESBLOQUEAR los campos para que el usuario pueda corregir
        const campos = ["matricula", "peso_vehicular", "numero_ejes_vehiculares", "numero_contenedores"];
        campos.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.readOnly = false;
                el.classList.remove("bg-light");
            }
        });

        // 3. Habilitar de nuevo los Selects
        const selects = ["modalidad_carroceria", "localidad_pertenece", "responsable_carroceria", "tipo_carroceria"];
        selects.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.pointerEvents = "auto";
                el.style.backgroundColor = "";
            }
        });
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

/* =====================================================
   REGISTRO FINAL (CON MENSAJES DE ERROR CLAROS)
   ===================================================== */
function ejecutarRegistroFinal() {
    // REQUERIMIENTO: Mensajes de error claros y específicos
    const errorMsg = validarFormularioCompleto();
    if (errorMsg) {
        alerta("Campo Faltante o Inválido", errorMsg, "error");
        return;
    }

    confirmar("¿Registrar Carrocería?", "¿Deseas continuar con el registro?")
        .then(r => {
            if (!r.isConfirmed) return;

            const formulario = document.getElementById("formCarrocerias");
            
            // Habilitar campos temporalmente para que FormData los capture
            const elementosBloqueados = formulario.querySelectorAll('[readonly], :disabled');
            elementosBloqueados.forEach(el => el.disabled = false);

            apiRequest("registrar-carroceria", formulario)
                .then(res => res.text())
                .then(resp => {
                    manejarRespuestaCRUD(resp, "Carrocería registrada correctamente.");
                })
                .catch(err => {
                    console.error("Error:", err);
                    alerta("Error", "No se pudo conectar con el servidor", "error");
                });
        });
}


// ASEGÚRATE DE QUE LOS SELECTS NO ESTÉN DISABLED
// En la función donde pasas a la sección de detalles, usa esto:
function bloquearCamposSeccion1() {
    // En lugar de .disabled = true, usa esto:
    document.getElementById("matricula").readOnly = true;
    document.getElementById("modalidad_carroceria").style.pointerEvents = "none";
    document.getElementById("tipo_carroceria").style.pointerEvents = "none";
    // Esto hace que no se puedan editar pero que SÍ se envíen.
}

/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */
/* =====================================================
   2. CONSULTAR (READ) - VERSIÓN INTEGRAL CON SELECTS
   ===================================================== */
function configurarVistaConsultarCarrocerias() {
    const selectFiltros = document.getElementById("selectFiltro");
    const botonAgregarFiltro = document.getElementById("btnAddFiltro");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");
    const btnVolver = document.getElementById("btnVolver");

    if (!selectFiltros || !botonAgregarFiltro || !contenedorFiltros) return;

    // EVENTO AGREGAR FILTRO (Con lógica de Selects)
    botonAgregarFiltro.addEventListener("click", () => {
        const valorFiltro = selectFiltros.value;
        const textoFiltro = selectFiltros.options[selectFiltros.selectedIndex].text;

        if (!valorFiltro) {
            alerta("Filtros", "Selecciona un criterio de búsqueda", "warning");
            return;
        }

        const fila = document.createElement("div");
        fila.classList.add("filter-row", "mb-2", "d-flex", "gap-2", "animated", "fadeIn");
        
        // REINCORPORACIÓN DE LISTAS DESPLEGABLES
        fila.innerHTML = `
            <input type="text" class="form-control" value="${textoFiltro}" readonly style="width: 40%;">
            ${valorFiltro === 'modalidad_carroceria' ? `
            <select name="modalidad_carroceria" class="form-select" required style="width: 50%;">
                <option value="">Seleccione</option>
                <option value="Carretero">Carretero</option>
                <option value="Ferroviario">Ferroviario</option>
                <option value="Marítimo">Marítimo</option>
                <option value="Aéreo">Aéreo</option>
            </select>` : valorFiltro === 'estatus_carroceria' ? `
            <select name="estatus_carroceria" class="form-select" required style="width: 50%;">
                <option value="">Seleccione</option>
                <option value="Disponible">Disponible</option>
                <option value="Ensamblada">Ensamblada</option>
                <option value="En mantenimiento">En mantenimiento</option>
                <option value="En reparación">En reparación</option>
            </select>` : `
            <input type="text" class="form-control" name="${valorFiltro}" placeholder="Valor a buscar..." required style="width: 50%;">
            `}
            <button type="button" class="btn btn-danger delete-btn"><i class="fas fa-trash"></i></button>
        `;

        contenedorFiltros.appendChild(fila);
        contenedorBotonConsultar.style.display = "flex";

        // Deshabilitar opción en el combo principal
        const opcionOriginal = selectFiltros.querySelector(`option[value="${valorFiltro}"]`);
        if (opcionOriginal) opcionOriginal.disabled = true;
        selectFiltros.selectedIndex = 0;

        // Botón eliminar del filtro individual
        fila.querySelector(".delete-btn").addEventListener("click", () => {
            fila.remove();
            if (opcionOriginal) opcionOriginal.disabled = false;
            contenedorBotonConsultar.style.display = contenedorFiltros.children.length ? "flex" : "none";
        });
    });

    // BOTÓN VOLVER (Comportamiento Localidades)
    if (btnVolver) {
        btnVolver.addEventListener("click", () => {
            // 1. Limpiar contenedor de filtros
            contenedorFiltros.innerHTML = "";
            
            // 2. Habilitar todas las opciones del select principal
            const opciones = selectFiltros.querySelectorAll("option");
            opciones.forEach(opt => opt.disabled = false);
            selectFiltros.selectedIndex = 0;

            // 3. Resetear formulario y tabla
            const form = document.getElementById("formConsultarCarrocerias");
            if (form) form.reset();
            document.querySelector("#tablaCarrocerias tbody").innerHTML = "";

            // 4. Alternar vistas
            document.getElementById("formContainer").style.display = "block";
            document.getElementById("tablaResultados").style.display = "none";
            contenedorBotonConsultar.style.display = "none";
        });
    }
}

function consultarCarrocerias() {
    const form = document.getElementById("formConsultarCarrocerias");
    const contenedorFiltros = document.getElementById("filtrosContainer");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Recolectar filtros dinámicos (Inputs y Selects)
        const filtros = {};
        const elementos = contenedorFiltros.querySelectorAll('input:not([readonly]), select');
        
        elementos.forEach(el => {
            if (el.value.trim() !== "") {
                filtros[el.name] = el.value.trim();
            }
        });

        apiRequest("consultar-carrocerias", filtros)
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector("#tablaCarrocerias tbody");
                tbody.innerHTML = "";

                if (!data || data.length === 0) {
                    alerta("Sin resultados", "No se encontraron carrocerías", "info");
                    return;
                }

                data.forEach(carro => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${carro.matricula || ''}</td>
                        <td>${carro.modalidad_carroceria || carro.modalidad || ''}</td>
                        <td>${carro.tipo_carroceria || carro.tipo || ''}</td>
                        <td>
                            <span class="badge ${typeof getBadgeClass === 'function' ? getBadgeClass(carro.estatus_carroceria || carro.estatus) : 'bg-primary'}">
                                ${carro.estatus_carroceria || carro.estatus || ''}
                            </span>
                        </td>
                        <td>${carro.nombre_display_localidad || 'N/A'}</td> 
                        <td>${carro.nombre_completo_personal || 'N/A'}</td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById("formContainer").style.display = "none";
                document.getElementById("tablaResultados").style.display = "block";
            })
            .catch(err => {
                console.error("Error:", err);
                alerta("Error", "Error al procesar la respuesta del servidor", "error");
            });
    });
}

// Función auxiliar para colores de estatus
function getBadgeClass(estatus) {
    switch(estatus) {
        case 'Disponible': return 'bg-success';
        case 'Ensamblada': return 'bg-primary';
        case 'En mantenimiento': return 'bg-warning text-dark';
        case 'En reparación': return 'bg-danger';
        default: return 'bg-secondary';
    }


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
        // Usa tu función 'alerta' de alertas.js para el ÉXITO
        alerta("Éxito", mensajeExito, "success").then(() => {
            if (redireccion) window.location.href = redireccion;
            else location.reload();
        });
    } else {
        // Usa tu función 'alerta' de alertas.js para el ERROR
        // Esto pondrá automáticamente el botón rojo y el texto "Aceptar"
        alerta("Error", respuesta, "error");
    }
}