// =====================================================
//  CRUD DE VEHÍCULOS (Estructura basada en Localidades)
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
    cargarChoferes();

    // ---- 2. Consultar ----
    configurarVistaConsultarVehiculos();
    consultarVehiculos();

    // ---- 3. Actualizar ----
    actualizarVehiculos();

    // ---- 4. Eliminar ----
    configurarVistaEliminarVehiculos();
    eliminarVehiculos();

    // ---- Eventos Globales ----
    manejarCambioModalidad();
});

/* =====================================================
   1. REGISTRAR (CREATE)
   ===================================================== */

function configurarRegistroVehiculos() {
    const formulario = document.querySelector("#formVehiculos");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        // 1. Captura de valores básicos
        const modalidad = document.getElementById("modalidad_vehiculo")?.value;
        const nomenclatura = document.getElementById("nomenclatura")?.value.trim();
        
        // 2. Obtener los checkboxes marcados
        const seleccionados = document.querySelectorAll('input[name="ids_carrocerias[]"]:checked');
        
        // --- VALIDACIÓN TÉCNICA: Si no hay nada seleccionado, detener ---
        if (seleccionados.length === 0) {
            alerta("Error", "Debe seleccionar al menos una carrocería para este vehículo.", "error");
            return;
        }

        // 3. ANALIZAR TIPOS DE CARROCERÍAS PARA REGLAS DE NEGOCIO
        let tieneCarga = false;
        let tieneArrastre = false;
        let tieneMixta = false;
        const idsCarrocerias = [];

        seleccionados.forEach(cb => {
            idsCarrocerias.push(cb.value); // Guardamos el ID para el envío
            const fila = cb.closest('tr');
            const tipoTexto = fila.cells[1].textContent.toLowerCase(); // Convertimos a minúsculas para comparar mejor
            
            if (tipoTexto.includes("carga")) tieneCarga = true;
            if (tipoTexto.includes("arrastre")) tieneArrastre = true;
            if (tipoTexto.includes("mixta")) tieneMixta = true;
        });

        // --- APLICACIÓN DE TUS REGLAS DE NEGOCIO ---

        // REGLA 1: Marítimo o Aéreo solo 1 carrocería y debe ser MIXTA
        if (modalidad === 'Marítimo' || modalidad === 'Aéreo') {
            if (idsCarrocerias.length !== 1 || !tieneMixta) {
                alerta("Validación", "Para modalidad Marítimo/Aéreo debe seleccionar exactamente 1 carrocería de tipo MIXTA.", "warning");
                return;
            }
        }

        // REGLAS 3 y 4: Ferroviario o Carretero
        if (modalidad === 'Ferroviario' || modalidad === 'Carretero') {
            // Regla 4: Si es 1 sola, debe ser Mixta
            if (idsCarrocerias.length === 1 && !tieneMixta) {
                alerta("Validación", "En modalidad " + modalidad + ", si selecciona una sola unidad, esta debe ser MIXTA.", "warning");
                return;
            }

            // Regla 3: Si no es mixta, debe haber combinación de Carga y Arrastre
            if (!tieneMixta) {
                if (!tieneCarga || !tieneArrastre) {
                    alerta("Validación", "Configuración inválida. Debe seleccionar al menos una unidad de CARGA y una de ARRASTRE.", "warning");
                    return;
                }
            }
        }

        // REGLA 7: Nomenclatura obligatoria para Carretero/Ferroviario
        if ((modalidad === 'Carretero' || modalidad === 'Ferroviario') && !nomenclatura) {
            alerta("Campo Requerido", "La nomenclatura es obligatoria para la modalidad " + modalidad + ".", "warning");
            return;
        }

        // 4. ENVÍO AL SERVIDOR (Solución al error de guardado)
        confirmar("¿Registrar Vehículo?", "¿Deseas guardar esta configuración de unidad?")
            .then(r => {
                if (!r.isConfirmed) return;

                // Creamos el FormData a partir del formulario
                const formData = new FormData(formulario);

                // FORZAMOS la inclusión de los IDs de las carrocerías
                // Esto soluciona el mensaje "Error: Debe seleccionar al menos una carrocería"
                formData.delete('ids_carrocerias[]'); // Limpiamos por si acaso
                idsCarrocerias.forEach(id => {
                    formData.append('ids_carrocerias[]', id);
                });

                // Enviamos usando tu función apiRequest pero pasando el formData procesado
                apiRequest("registrar-vehiculo", formData)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(resp, "Vehículo registrado correctamente.", "dashboard.php"));
            });
    });
}

/**
 * Carga la lista de personal filtrando por el cargo de 'Chofer'
 */
function cargarChoferes() {
    const select = document.getElementById('chofer_asignado');
    if (!select) return;

    apiRequest('consultar-personal-chofer')
        .then(res => res.json())
        .then(data => {
            select.innerHTML = '<option value="">Seleccione un chofer</option>';
            
            if (!data || data.length === 0) {
                select.innerHTML = '<option value="">No hay choferes disponibles</option>';
                return;
            }

            data.forEach(chofer => {
                let opt = document.createElement('option');
                opt.value = chofer.id_personal;
                
                // Concatenación usando los nombres de columna de la BD (init.sql)
                const nombreCompleto = [
                    chofer.nombre_personal,
                    chofer.apellido_paterno,
                    chofer.apellido_materno
                ].filter(Boolean).join(' '); 
                
                opt.textContent = nombreCompleto;
                select.appendChild(opt);
            });
        })
        .catch(err => {
            select.innerHTML = '<option value="">Error al cargar choferes</option>';
            console.error("Error:", err);
        });
}

/**
 * Carga las carrocerías disponibles filtradas por la modalidad elegida
 */
// Localiza la función cargarCarrocerias en vehiculos.js
function cargarCarrocerias(modalidad) {
    const contenedor = document.getElementById('lista_carrocerias');
    if (!contenedor) return;

    contenedor.innerHTML = '<tr><td colspan="3" class="text-center">Cargando...</td></tr>';

    apiRequest('consultar-carrocerias-por-modalidad', { modalidad: modalidad })
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                contenedor.innerHTML = '<tr><td colspan="3" class="text-center">No hay unidades disponibles para esta modalidad</td></tr>';
                return;
            }

            contenedor.innerHTML = ''; // Limpiar
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="checkbox" name="ids_carrocerias[]" value="${item.id_carroceria}" class="form-check-input">
                    </td>
                    <td>${item.tipo_carroceria}</td>
                    <td>${item.matricula}</td>
                `;
                contenedor.appendChild(tr);
            });
        })
        .catch(err => {
            console.error("Error al cargar carrocerías:", err);
            contenedor.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al conectar con el servidor</td></tr>';
        });
}

/* =====================================================
   2. CONSULTAR (READ)
   ===================================================== */

/**
 * Mismo sistema de filtros dinámicos que localidades.js
 */
function configurarVistaConsultarVehiculos() {
    const selectFiltros = document.getElementById("selectFiltro");
    const botonAgregarFiltro = document.getElementById("btnAddFiltro");
    const contenedorFiltros = document.getElementById("filtrosContainer");
    const contenedorBotonConsultar = document.getElementById("contenedorConsultar");

    if (!selectFiltros || !botonAgregarFiltro) return;

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
    const contenedorResultados = document.getElementById("tablaResultados");
    const cuerpoTabla = document.querySelector("#tablaVehiculos tbody");
    const botonVolver = document.getElementById("btnVolver");

    if (!formularioConsulta) return;

    formularioConsulta.addEventListener("submit", (e) => {
        e.preventDefault();

        // Recolectar filtros dinámicos según el SQL (clave_vehiculo, modalidad, etc)
        const filtros = {};
        const inputs = contenedorFiltros.querySelectorAll('input[name]');
        inputs.forEach(input => {
            if (input.value.trim()) filtros[input.name] = input.value.trim();
        });

        apiRequest("consultar-vehiculos", filtros)
            .then(res => res.json())
            .then(datos => {
                if (!datos || datos.length === 0) {
                    alerta("Consulta", "No se encontraron vehículos.", "warning");
                    return;
                }

                cuerpoTabla.innerHTML = "";
                datos.forEach(v => {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${v.clave_vehiculo}</td>
                        <td>${v.modalidad_vehiculo}</td>
                        <td>${v.nomenclatura || 'N/A'}</td>
                        <td>${v.peso_bruto_vehicular} t</td>
                        <td>${v.descripcion_vehiculo}</td>
                    `;
                    cuerpoTabla.appendChild(fila);
                });

                contenedorResultados.style.display = "block";
                formularioConsulta.parentElement.style.display = "none";
            })
            .catch(() => alerta("Error", "Problema al consultar los datos.", "error"));
    });

    botonVolver?.addEventListener("click", () => {
        contenedorResultados.style.display = "none";
        formularioConsulta.parentElement.style.display = "block";
        contenedorFiltros.innerHTML = "";
        document.getElementById("contenedorConsultar").style.display = "none";
        const selectFiltros = document.getElementById("selectFiltro");
        if (selectFiltros) {
            selectFiltros.selectedIndex = 0;
            Array.from(selectFiltros.options).forEach(opt => opt.disabled = false);
        }
    });
}

/* =====================================================
   3. ACTUALIZAR (UPDATE)
   ===================================================== */

function actualizarVehiculos() {
    const inputBusqueda = document.getElementById('inputBuscarVehiculo');
    const datalist = document.getElementById('listaVehiculos'); // datalist en HTML
    const contenedorBusqueda = document.getElementById('contenedorBusqueda');
    const contenedorBotones = document.getElementById('contenedorBotones');
    const formulario = document.getElementById('formActualizarVehiculo');

    if (!inputBusqueda) return;

    inputBusqueda.addEventListener('input', () => {
        const texto = inputBusqueda.value.trim();
        if (texto.length < 2) return;

        // Usamos la columna 'clave_vehiculo' del SQL para buscar
        apiRequest('buscar-vehiculos', { busqueda: texto })
            .then(r => r.json())
            .then(vehiculos => {
                datalist.innerHTML = '';
                vehiculos.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.clave_vehiculo; // Lo que ve el usuario
                    // Guardamos todo el objeto SQL en el dataset
                    Object.keys(v).forEach(key => opt.dataset[key] = v[key]);
                    datalist.appendChild(opt);
                });
            });
    });

    inputBusqueda.addEventListener('change', () => {
        const opcion = Array.from(datalist.options).find(opt => opt.value === inputBusqueda.value);
        if (!opcion) return;

        // Llenado automático basado en las columnas del SQL
        document.getElementById('inputIdVehiculo').value = opcion.dataset.id_vehiculo;
        document.getElementById('inputClave').value = opcion.dataset.clave_vehiculo;
        document.getElementById('inputClase').value = opcion.dataset.clase;
        document.getElementById('inputNomenclatura').value = opcion.dataset.nomenclatura;
        document.getElementById('inputPeso').value = opcion.dataset.peso_bruto_vehicular;
        document.getElementById('inputEjes').value = opcion.dataset.numero_de_ejes;
        document.getElementById('inputLlantas').value = opcion.dataset.numero_de_llantas;
        
        const modSelect = document.getElementById('modalidad_vehiculo');
        modSelect.value = opcion.dataset.modalidad_vehiculo;
        modSelect.dispatchEvent(new Event('change')); // Activa visibilidad de campos

        contenedorBusqueda.classList.add('oculto');
        contenedorBotones.style.display = 'block';
    });

    formulario.addEventListener('submit', e => {
        if (!formulario.checkValidity()) return;
        e.preventDefault();

        confirmar("¿Actualizar unidad?", "Se guardarán los cambios en el sistema.")
            .then(r => {
                if (!r.isConfirmed) return;
                apiRequest("actualizar-vehiculo", formulario)
                    .then(r => r.text())
                    .then(resp => manejarRespuestaCRUD(resp, "Unidad actualizada correctamente.", "actualizar-vehiculos.php"));
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
        // Ocultar campos (id_vehiculo o clave_vehiculo según SQL)
        document.getElementById('campoId').style.display = (this.value === 'id') ? 'block' : 'none';
        document.getElementById('campoClave').style.display = (this.value === 'clave') ? 'block' : 'none';
    });
}

function eliminarVehiculos() {
    const formulario = document.getElementById('formConsultaEliminar');
    const contFiltros = document.getElementById('filtroEliminar');
    const contResultados = document.getElementById('resultadosBusqueda');

    if (!formulario) return;

    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        const filtro = document.getElementById('filtroBusqueda').value;
        const valor = (filtro === 'id') ? document.getElementById('inputIdBusqueda').value : document.getElementById('inputClaveBusqueda').value;

        apiRequest("mostrar-eliminar-vehiculo", { [filtro]: valor })
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    alerta("No encontrado", "No existe vehículo con esos datos.", "warning");
                    return;
                }
                
                const v = data[0];
                contFiltros.style.display = "none";
                contResultados.style.display = "block";

                // Mapeo con nombres del SQL
                document.getElementById("res_id").value = v.id_vehiculo;
                document.getElementById("res_clave").value = v.clave_vehiculo;
                document.getElementById("res_modalidad").value = v.modalidad_vehiculo;

                document.getElementById("btnEliminar").onclick = () => {
                    confirmar("¿Eliminar unidad?", "Esta acción no se puede revertir.", "warning")
                        .then(res => {
                            if (!res.isConfirmed) return;
                            apiRequest("eliminar-vehiculo", { id_vehiculo: v.id_vehiculo })
                                .then(r => r.text())
                                .then(resp => manejarRespuestaCRUD(resp, "Vehículo eliminado.", "dashboard.php"));
                        });
                };
            });
    });
}

/* =====================================================
   5. FUNCIONES REUTILIZABLES Y UX
   ===================================================== */

/**
 * Regla 6: Visibilidad Condicional (Solo Carretero ve Ejes/Llantas)
 */
function manejarCambioModalidad() {
    const selectMod = document.getElementById("modalidad_vehiculo");
    const divLlantas = document.getElementById("div_llantas");
    const divEjes = document.getElementById("div_ejes");

    if (!selectMod) return;

    selectMod.addEventListener("change", function() {
        const mod = this.value;
        const esCarretero = (mod === "Carretero");
        
        // 1. Visibilidad Condicional
        if(divLlantas) divLlantas.style.display = esCarretero ? "block" : "none";
        if(divEjes) divEjes.style.display = esCarretero ? "block" : "none";

        // 2. Limpieza si no es carretero
        if (!esCarretero) {
            if(document.getElementById("numero_de_llantas")) document.getElementById("numero_de_llantas").value = 0;
            if(document.getElementById("numero_de_ejes")) document.getElementById("numero_de_ejes").value = 0;
        }

        // 3. Cargar Carrocerías correspondientes
        if(mod) {
            cargarCarrocerias(mod);
        } else {
            document.getElementById('lista_carrocerias').innerHTML = '<tr><td colspan="3" class="text-center">Seleccione una modalidad</td></tr>';
        }
    });
}


function apiRequest(accion, datos = null) {
    let formData;

    // SI DATOS YA ES UN FORMDATA (el que armaste manualmente), ÚSALO
    if (datos instanceof FormData) {
        formData = datos;
    } 
    // Si es el elemento <form> directamente
    else if (datos instanceof HTMLFormElement) {
        formData = new FormData(datos);
    } 
    // Si es un objeto plano {clave: valor}
    else {
        formData = new FormData();
        if (datos) {
            for (const clave in datos) {
                if (Array.isArray(datos[clave])) {
                    datos[clave].forEach(valor => formData.append(clave, valor));
                } else {
                    formData.append(clave, datos[clave]);
                }
            }
        }
    }

    // Agregamos la acción al final
    formData.set("action", accion);

    return fetch('/ajax/vehiculo-ajax.php', { 
        method: "POST", 
        body: formData 
    });
}

function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {
    if (respuesta.trim() === "OK") {
        alerta("Éxito", mensajeExito, "success")
            .then(() => { if (redireccion) window.location.href = redireccion; });
    } else {
        alerta("Error", respuesta, "error");
    }
}