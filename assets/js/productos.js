/********************************************
 *      INICIALIZADOR PRINCIPAL
 ********************************************/

document.addEventListener("DOMContentLoaded", () => {

    const formRegistro = document.getElementById("formRegistroProductos");

    if (formRegistro) {
        cargarTiposEmbalaje();
        cargarTiposMercancia();
        cargarLocalidadesEnSelect();
        initValidacionesProductos()
        configurarRegistroProductos();
    }

    // ACTUALIZAR PRODUCTOS
    if (document.getElementById("buscador_producto")) {
        configurarActualizarProducto();
    }

    // ELIMINAR PRODUCTOS - Detectar por el input de búsqueda
    if (document.getElementById("producto_input")) {
        configurarEliminarProducto();
    }

    // CONFIGURAR CONSULTA SI EXISTE EL FORMULARIO DE CONSULTA
    if (document.getElementById("formConsultaProductos")) {
        initConsultaProductos();
    }

});

/********************************************
 *      LISTAS ESTÁTICAS (CATÁLOGOS)
 ********************************************/

const tiposEmbalaje = [
    "Envase simple", "Envase combinado", "Envase interior", "Envase exterior",
    "Envase intermedio", "Recipiente intermedio para granel (RIG / IBC)", "Gran embalaje (LP)",
    "Tambor metálico", "Tambor de plástico", "Bidón metálico", "Bidón de plástico", "Caja de madera",
    "Caja de cartón / fibra", "Caja de plástico", "Caja metálica",
    "Bolsa de plástico", "Bolsa de papel", "Frasco o botella de vidrio", "Frasco o botella de plástico",
    "Garrafa o contenedor de vidrio / gres", "Contenedor a presión (cilindro, tanque portátil)",
    "Embalaje compuesto (varios materiales)", "Embalaje con flejes o ligaduras",
    "Embalaje interior rígido", "Envase reutilizable o retornable", "Embalaje para cantidades limitadas",
    "Embalaje para residuos peligrosos", "Embalaje especial para líquidos corrosivos",
    "Embalaje especial para materiales explosivos"
];

const tiposMercancia = [
    "Mercancías peligrosas", "Sustancias peligrosas", "Materiales peligrosos", "Residuos peligrosos",
    "Mercancías en cantidades limitadas", "Mercancías en cantidades exceptuadas", "Mercancías no peligrosas",
    "Mercancías comunes / generales", "Mercancías para consumo final", "Mercancías a granel",
    "Mercancías en envases especiales", "Mercancías transportadas en tanque/autotanque",
    "Clase 1 - Explosivos", "Clase 2 - Gases", "Clase 3 - Líquidos inflamables",
    "Clase 4 - Sólidos inflamables / combustión espontánea / reacción con agua",
    "Clase 5 - Sustancias comburentes y peróxidos orgánicos", "Clase 6 - Sustancias tóxicas e infecciosas",
    "Clase 7 - Materiales radiactivos", "Clase 8 - Sustancias corrosivas",
    "Clase 9 - Sustancias peligrosas varias / misceláneas"
];


/********************************************
 *      CARGA DE SELECTS (EMBALAJE / MERCANCÍA / LOCALIDADES)
 ********************************************/

function cargarTiposEmbalaje() {
    const select = document.getElementById("tipo_embalaje");
    if (!select) return;

    tiposEmbalaje.forEach(tipo => {
        const opt = document.createElement("option");
        opt.value = tipo;
        opt.textContent = tipo;
        select.appendChild(opt);
    });
}

function cargarTiposMercancia() {
    const select = document.getElementById("tipo_mercancia");
    if (!select) return;

    tiposMercancia.forEach(tipo => {
        const opt = document.createElement("option");
        opt.value = tipo;
        opt.textContent = tipo;
        select.appendChild(opt);
    });
}

/* Reutilizable para filtros: carga en cualquier select de localidades dado su id */
function cargarLocalidadesEnSelect(selectId = "ubicacion_producto") {
    const select = document.getElementById(selectId);
    if (!select) return;

    apiRequestProductos("listar_localidades")
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                if (typeof alerta === "function") alerta("Error", data.error, "error");
                return;
            }
            // mantener la primera opción si existe
            // limpiar existencias (excepto placeholder)
            // si quieres mantener placeholder deja innerHTML con la primera opción
            const placeholder = select.querySelector('option[value=""]') ? select.querySelector('option[value=""]').outerHTML : '<option value="">Seleccione una localidad</option>';
            select.innerHTML = placeholder;
            data.forEach(loc => {
                const opt = document.createElement("option");
                opt.value = loc.id_localidad;
                opt.textContent = `${loc.nombre_centro_trabajo} (${loc.estado || ''})`;
                select.appendChild(opt);
            });
        })
        .catch(err => console.error("Error cargando localidades:", err));
}


/********************************************
 *     REGISTRO DE PRODUCTOS (CRUD)
 ********************************************/

function configurarRegistroProductos() {

    const formulario = document.getElementById("formRegistroProductos");
    if (!formulario) return;

    formulario.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validación global
        if (!validarFormularioProductos()) return;

        confirmar("¿Registrar Producto?", "¿Deseas continuar?")
            .then(r => {
                if (!r.isConfirmed) return;

                apiRequestProductos("registrar", formulario)
                    .then(r => r.text())
                    .then(resp => {
                        manejarRespuestaCRUD(
                            resp,
                            "Producto registrado correctamente.",
                            null
                        );
                        // Limpia los campos
                        formulario.reset();

                        // Limpia errores visuales si existen
                        document.querySelectorAll('.error-message').forEach(e => e.classList.remove('show'));
                        document.querySelectorAll('.error').forEach(e => e.classList.remove('error'));

                        // Limpia el peso volumétrico
                        document.getElementById("peso_volumetrico").value = "";
                    })
                    .catch(() =>
                        alerta("Error", "Ocurrió un error al registrar el producto.", "error")
                    );
            });
    });
}

/********************************************
  *      CONSULTA DE PRODUCTOS 
  ********************************************/

function initConsultaProductos() {
    const form = document.getElementById("formConsultaProductos");
    if (!form) return;

    // enlazar comportamiento
    configurarConsultaProductos();
}

function configurarConsultaProductos() {
    const form = document.getElementById("formConsultaProductos");
    if (!form) return;

    // elementos (debe coincidir con IDs en la vista)
    const inputNombre = document.getElementById("nombre_producto_consulta");
    const selectFiltro = document.getElementById("filtro_busqueda");

    const containerUbic = document.getElementById("filter_ubicacion_container");
    const containerTipo = document.getElementById("filter_tipo_mercancia_container");
    const containerPeso = document.getElementById("filter_peso_container");
    const containerExist = document.getElementById("filter_existencia_container");

    const selectUbic = document.getElementById("filter_ubicacion");
    const selectTipo = document.getElementById("filter_tipo_mercancia");
    const selectPeso = document.getElementById("filter_peso");
    const inputExist = document.getElementById("filter_existencia");

    const tablaCont = document.getElementById("tablaResultadosProductos");
    const tbody = document.getElementById("tbodyResultadosProductos");
    const btnVolver = document.getElementById("btnVolverResultados");

    // inicializar opciones estáticas (tipo mercancía) y localidades para filtro ubicación
    if (selectTipo) {
        // añadir placeholder si no tiene
        if (!selectTipo.querySelector('option[value=""]')) selectTipo.innerHTML = '<option value="">Seleccione</option>';
        tiposMercancia.forEach(t => {
            const opt = document.createElement('option'); opt.value = t; opt.textContent = t;
            selectTipo.appendChild(opt);
        });
    }
    cargarLocalidadesEnSelect("filter_ubicacion");

    // función para mostrar/ocultar filtros secundarios
    function onFiltroChange() {
        const v = selectFiltro?.value || '';
        if (containerUbic) containerUbic.style.display = (v === 'ubicacion') ? 'block' : 'none';
        if (containerTipo) containerTipo.style.display = (v === 'tipo_mercancia') ? 'block' : 'none';
        if (containerPeso) containerPeso.style.display = (v === 'peso') ? 'block' : 'none';
        if (containerExist) containerExist.style.display = (v === 'existencia') ? 'block' : 'none';
    }

    // inicial
    if (selectFiltro) selectFiltro.addEventListener('change', onFiltroChange);
    onFiltroChange();

    // manejar submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const payload = {};
        const nombre = inputNombre?.value?.trim();
        if (nombre) payload.nombre_producto = nombre;

        const filtro = selectFiltro?.value || '';
        payload.filtro = filtro;

        if (filtro === 'ubicacion') {
            payload.id_localidad = selectUbic?.value || '';
        } else if (filtro === 'tipo_mercancia') {
            payload.tipo_mercancia = selectTipo?.value || '';
        } else if (filtro === 'peso') {
            payload.rango_peso = selectPeso?.value || '';
        } else if (filtro === 'existencia') {
            payload.cantidad_existencia = inputExist?.value || '';
        }

        // enviar petición al backend usando la misma función apiRequestProductos
        apiRequestProductos("consultar", payload)
            .then(r => r.json())
            .then(data => {
                if (!data || data.error) {
                    if (data && data.error) {
                        if (typeof alerta === "function") alerta("Error", data.error, "error");
                        else alert(data.error);
                    } else {
                        if (typeof alerta === "function") alerta("Info", "No se encontraron resultados", "info");
                        else alert("No se encontraron resultados");
                    }
                    return;
                }
                renderResultadosConsulta(data, tbody, tablaCont, form);
            })
            .catch(err => {
                console.error("Error consulta productos:", err);
                if (typeof alerta === "function") alerta("Error", "Ocurrió un error en la consulta", "error");
                else alert("Ocurrió un error en la consulta");
            });
    });

    // volver
    if (btnVolver) {
        btnVolver.addEventListener("click", function () {
            tablaCont.style.display = 'none';
            form.style.display = 'block';
            // limpiar tabla
            if (tbody) tbody.innerHTML = '';
        });
    }
}

// render de resultados (reutilizable)
function renderResultadosConsulta(items, tbody, tablaCont, form) {
    if (!tbody) return;
    tbody.innerHTML = '';

    if (!Array.isArray(items) || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7">No se encontraron resultados</td></tr>';
    } else {
        items.forEach(it => {
            const tr = document.createElement('tr');

            const tdNombre = document.createElement('td');
            tdNombre.textContent = it.nombre_producto || '';
            tr.appendChild(tdNombre);

            const tdUbic = document.createElement('td');
            tdUbic.textContent = it.nombre_centro_trabajo || '';
            tr.appendChild(tdUbic);

            const tdTipoM = document.createElement('td');
            tdTipoM.textContent = it.tipo_de_mercancia || '';
            tr.appendChild(tdTipoM);

            const tdTipoE = document.createElement('td');
            tdTipoE.textContent = it.tipo_de_embalaje || '';
            tr.appendChild(tdTipoE);

            const tdPeso = document.createElement('td');
            tdPeso.textContent = (it.peso !== null && it.peso !== undefined) ? it.peso : '';
            tr.appendChild(tdPeso);

            const tdUnidades = document.createElement('td');
            tdUnidades.textContent = (it.unidades_existencia !== null && it.unidades_existencia !== undefined) ? it.unidades_existencia : '';
            tr.appendChild(tdUnidades);

            const tdTipoInst = document.createElement('td');
            tdTipoInst.textContent = it.tipo_instalacion || '';
            tr.appendChild(tdTipoInst);

            tbody.appendChild(tr);
        });
    }

    // mostrar tabla y ocultar formulario
    if (tablaCont) tablaCont.style.display = 'block';
    if (form) form.style.display = 'none';
}


/********************************************
 *      ACTUALIZAR PRODUCTOS
 ********************************************/

function configurarActualizarProducto() {
    const buscador = document.getElementById("buscador_producto");
    const sugerenciasDiv = document.getElementById("sugerencias");

    if (!buscador || !sugerenciasDiv) return;

    let timeoutBusqueda = null;

    // Buscar productos mientras el usuario escribe
    buscador.addEventListener("input", function () {
        const termino = this.value.trim();

        // Limpiar timeout anterior
        clearTimeout(timeoutBusqueda);

        if (termino.length < 2) {
            sugerenciasDiv.classList.remove("activo");
            sugerenciasDiv.innerHTML = "";
            return;
        }

        // Esperar 300ms después de que el usuario deje de escribir
        timeoutBusqueda = setTimeout(() => {
            buscarProductosSugerencias(termino);
        }, 300);
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener("click", function (e) {
        if (!buscador.contains(e.target) && !sugerenciasDiv.contains(e.target)) {
            sugerenciasDiv.classList.remove("activo");
        }
    });
}

// Función para buscar productos y mostrar sugerencias
function buscarProductosSugerencias(termino) {
    const sugerenciasDiv = document.getElementById("sugerencias");

    apiRequestProductos("buscar_productos", { termino: termino })
        .then(r => r.json())
        .then(data => {
            sugerenciasDiv.innerHTML = "";

            if (data.error) {
                sugerenciasDiv.innerHTML = `<div class="no-resultados">${data.error}</div>`;
                sugerenciasDiv.classList.add("activo");
                return;
            }

            if (data.length === 0) {
                sugerenciasDiv.innerHTML = '<div class="no-resultados">No se encontraron productos</div>';
                sugerenciasDiv.classList.add("activo");
                return;
            }

            data.forEach(producto => {
                const item = document.createElement("div");
                item.className = "sugerencia-item";
                item.innerHTML = `
                    <span class="sugerencia-nombre">${producto.nombre_producto}</span>
                    <span class="sugerencia-id">(ID: ${producto.id_producto})</span>
                `;

                item.addEventListener("click", () => {
                    cargarProductoEnFormulario(producto.id_producto);
                    sugerenciasDiv.classList.remove("activo");
                    document.getElementById("buscador_producto").value = producto.nombre_producto;
                });

                sugerenciasDiv.appendChild(item);
            });

            sugerenciasDiv.classList.add("activo");
        })
        .catch(err => {
            console.error("Error en búsqueda:", err);
            sugerenciasDiv.innerHTML = '<div class="no-resultados">Error al buscar productos</div>';
            sugerenciasDiv.classList.add("activo");
        });
}

// Función para cargar producto completo en el formulario
function cargarProductoEnFormulario(idProducto) {
    apiRequestProductos("obtener_producto", { id_producto: idProducto })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alerta("Error", data.error, "error");
                return;
            }

            // Llenar todos los campos del formulario
            document.getElementById("id_producto").value = data.id_producto || "";
            document.getElementById("nombre_producto").value = data.nombre_producto || "";
            document.getElementById("cajas_por_cama").value = data.cajas_por_cama || "";
            document.getElementById("peso").value = data.peso || "";
            document.getElementById("altura").value = data.altura || "";
            // largo y ancho se dejan vacíos para que el usuario los ingrese si quiere recalcular
            document.getElementById("largo").value = "";
            document.getElementById("ancho").value = "";
            document.getElementById("peso_volumetrico").value = data.peso_volumetrico || "";
            document.getElementById("tipo_de_embalaje").value = data.tipo_de_embalaje || "";
            document.getElementById("ubicacion_producto").value = data.ubicacion_producto || "";
            document.getElementById("camas_por_pallet").value = data.camas_por_pallet || "";
            document.getElementById("peso_soportado").value = data.peso_soportado || "";
            document.getElementById("unidades_existencia").value = data.unidades_existencia || "";
            document.getElementById("tipo_de_mercancia").value = data.tipo_de_mercancia || "";

            // Mostrar botones de acción
            document.querySelector(".botones").style.display = "flex";
            document.querySelector(".botones").style.justifyContent = "center";
            document.querySelector(".botones").style.gap = "20px";
            document.querySelector(".botones").style.marginTop = "30px";

            alerta("Éxito", "Producto cargado. Puede editar los campos.", "success");
        })
        .catch(err => {
            console.error("Error al cargar producto:", err);
            alerta("Error", "No se pudo cargar el producto", "error");
        });
}

// Función para calcular peso volumétrico
/*function calcularPesoVolumetrico() {
    const altura = parseFloat(document.getElementById("altura").value) || 0;
    const largo = parseFloat(document.getElementById("largo").value) || 0;
    const ancho = parseFloat(document.getElementById("ancho").value) || 0;

    if (altura > 0 && largo > 0 && ancho > 0) {
        // Fórmula: (Altura × Largo × Ancho) / 5000
        const pesoVolumetrico = (altura * largo * ancho) / 5000;
        document.getElementById("peso_volumetrico").value = pesoVolumetrico.toFixed(2);
    } else {
        document.getElementById("peso_volumetrico").value = "";
    }
}*/

// Función para guardar cambios del producto
function guardarProducto() {
    const idProducto = document.getElementById("id_producto").value;

    if (!idProducto) {
        alerta("Error", "No hay ningún producto seleccionado", "error");
        return;
    }

    // Crear objeto con los datos del formulario (SIN largo y ancho)
    const datos = {
        id_producto: idProducto,
        nombre_producto: document.getElementById("nombre_producto").value.trim(),
        cajas_por_cama: document.getElementById("cajas_por_cama").value,
        peso: document.getElementById("peso").value,
        altura: document.getElementById("altura").value,
        peso_volumetrico: document.getElementById("peso_volumetrico").value,
        tipo_de_embalaje: document.getElementById("tipo_de_embalaje").value,
        ubicacion_producto: document.getElementById("ubicacion_producto").value,
        camas_por_pallet: document.getElementById("camas_por_pallet").value,
        peso_soportado: document.getElementById("peso_soportado").value,
        unidades_existencia: document.getElementById("unidades_existencia").value,
        tipo_de_mercancia: document.getElementById("tipo_de_mercancia").value
    };

    // Validar campos obligatorios
    if (!datos.nombre_producto || !datos.ubicacion_producto) {
        alerta("Error", "Complete todos los campos obligatorios", "error");
        return;
    }

    confirmar("¿Actualizar Producto?", "¿Deseas guardar los cambios?")
        .then(r => {
            if (!r.isConfirmed) return;

            apiRequestProductos("actualizar_producto", datos)
                .then(r => r.text())
                .then(resp => {
                    if (resp.trim() === "OK") {
                        alerta("Éxito", "Producto actualizado correctamente", "success");
                        limpiarFormulario();
                    } else {
                        alerta("Error", resp, "error");
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alerta("Error", "Ocurrió un error al actualizar el producto", "error");
                });
        });
}

// Función para limpiar el formulario
function limpiarFormulario() {
    document.getElementById("buscador_producto").value = "";
    document.getElementById("id_producto").value = "";
    document.getElementById("nombre_producto").value = "";
    document.getElementById("cajas_por_cama").value = "";
    document.getElementById("peso").value = "";
    document.getElementById("altura").value = "";
    document.getElementById("largo").value = "";
    document.getElementById("ancho").value = "";
    document.getElementById("peso_volumetrico").value = "";
    document.getElementById("tipo_de_embalaje").value = "";
    document.getElementById("ubicacion_producto").value = "";
    document.getElementById("camas_por_pallet").value = "";
    document.getElementById("peso_soportado").value = "";
    document.getElementById("unidades_existencia").value = "";
    document.getElementById("tipo_de_mercancia").value = "";

    document.querySelector(".botones").style.display = "none";
    document.getElementById("sugerencias").classList.remove("activo");
}


// productos.js - Eliminar la validación del btnBuscar

function configurarEliminarProducto() {
    console.log("🔧 Configurando eliminar producto...");

    const btnEliminar = document.getElementById("btnEliminar");
    const btnCancelar = document.getElementById("btnCancelar");
    const productoInput = document.getElementById("producto_input");
    const sugerenciasDiv = document.getElementById("sugerencias_eliminar");

    console.log("Elementos encontrados:", {
        btnEliminar: !!btnEliminar,
        productoInput: !!productoInput,
        sugerenciasDiv: !!sugerenciasDiv
    });

    // Verificar que estamos en la página de eliminar (SIN btnBuscar)
    if (!btnEliminar || !productoInput || !sugerenciasDiv) {
        console.log("❌ Faltan elementos, saliendo...");
        return;
    }

    console.log("✅ Todos los elementos encontrados");

    let timeoutBusqueda = null;

    // Buscar productos mientras el usuario escribe
    productoInput.addEventListener("input", function () {
        const termino = this.value.trim();
        console.log("📝 Input cambió:", termino);

        clearTimeout(timeoutBusqueda);

        if (termino.length < 2) {
            console.log("⚠️ Término muy corto");
            sugerenciasDiv.classList.remove("activo");
            sugerenciasDiv.innerHTML = "";
            return;
        }

        console.log("⏳ Esperando 300ms para buscar...");
        timeoutBusqueda = setTimeout(() => {
            console.log("🔍 Buscando sugerencias para:", termino);
            buscarProductosSugerenciasEliminar(termino);
        }, 300);
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener("click", function (e) {
        if (!productoInput.contains(e.target) && !sugerenciasDiv.contains(e.target)) {
            sugerenciasDiv.classList.remove("activo");
        }
    });

    // Eliminar producto
    btnEliminar.addEventListener("click", function () {
        console.log("🗑️ Botón eliminar clickeado");
        confirmarEliminacionProducto();
    });

    // Cancelar eliminación
    if (btnCancelar) {
        btnCancelar.addEventListener("click", function () {
            console.log("❌ Botón cancelar clickeado");
            limpiarFormularioEliminarProducto();
        });
    }
}

// Función para cargar todos los productos en el select
function cargarProductosParaEliminar() {
    const select = document.getElementById("producto_select");

    if (!select) return;

    apiRequestProductos("listar_todos_productos")
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alerta("Error", data.error, "error");
                return;
            }

            // Limpiar select
            select.innerHTML = '<option value="">Seleccione un producto</option>';

            // Agregar productos
            data.forEach(producto => {
                const option = document.createElement("option");
                option.value = producto.id_producto;
                option.textContent = `${producto.nombre_producto} - ID: ${producto.id_producto}`;
                select.appendChild(option);
            });
        })
        .catch(err => {
            console.error("Error al cargar productos:", err);
            alerta("Error", "No se pudieron cargar los productos", "error");
        });
}

// Función para buscar y mostrar los datos del producto
function buscarProductoParaEliminar(idProducto) {
    if (!idProducto) {
        alerta("Error", "Seleccione un producto", "error");
        return;
    }

    apiRequestProductos("obtener_producto", { id_producto: idProducto })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alerta("Error", data.error, "error");
                return;
            }

            mostrarDatosProductoEliminar(data);
        })
        .catch(err => {
            console.error("Error al buscar producto:", err);
            alerta("Error", "No se pudo cargar el producto", "error");
        });
}

function buscarProductosSugerenciasEliminar(termino) {
    console.log("🔎 buscarProductosSugerenciasEliminar llamada con:", termino);

    const sugerenciasDiv = document.getElementById("sugerencias_eliminar");

    if (!sugerenciasDiv) {
        console.error("❌ No se encontró sugerencias_eliminar");
        return;
    }

    console.log("📡 Haciendo petición AJAX...");

    apiRequestProductos("buscar_productos", { termino: termino })
        .then(r => {
            console.log("📨 Respuesta recibida:", r);
            return r.json();
        })
        .then(data => {
            console.log("📦 Datos parseados:", data);

            sugerenciasDiv.innerHTML = "";

            if (data.error) {
                console.warn("⚠️ Error en respuesta:", data.error);
                sugerenciasDiv.innerHTML = `<div class="no-resultados">${data.error}</div>`;
                sugerenciasDiv.classList.add("activo");
                return;
            }

            if (data.length === 0) {
                console.log("📭 No se encontraron productos");
                sugerenciasDiv.innerHTML = '<div class="no-resultados">No se encontraron productos</div>';
                sugerenciasDiv.classList.add("activo");
                return;
            }

            console.log(`✅ ${data.length} productos encontrados`);

            data.forEach(producto => {
                const item = document.createElement("div");
                item.className = "sugerencia-item";
                item.innerHTML = `
                    <div class="sugerencia-nombre">${producto.nombre_producto}</div>
                    <div class="sugerencia-id">ID: ${producto.id_producto}</div>
                `;

                item.addEventListener("click", () => {
                    console.log("✅ Producto seleccionado:", producto);

                    // Guardar producto seleccionado
                    window.productoSeleccionadoEliminar = producto;

                    // Actualizar input
                    document.getElementById("producto_input").value = producto.nombre_producto;

                    // Cerrar sugerencias
                    sugerenciasDiv.classList.remove("activo");

                    // Cargar datos automáticamente
                    buscarProductoParaEliminar(producto.id_producto);
                });

                sugerenciasDiv.appendChild(item);
            });

            sugerenciasDiv.classList.add("activo");
            console.log("✅ Sugerencias mostradas");
        })
        .catch(err => {
            console.error("❌ Error en búsqueda:", err);
            sugerenciasDiv.innerHTML = '<div class="no-resultados">Error al buscar productos</div>';
            sugerenciasDiv.classList.add("activo");
        });
}

// Función para mostrar los datos del producto en la sección de resultados
function mostrarDatosProductoEliminar(producto) {
    // Guardar el ID del producto
    document.getElementById("id_producto").value = producto.id_producto;

    // Mostrar los datos
    document.getElementById("display_nombre").textContent = producto.nombre_producto || "-";
    document.getElementById("display_ubicacion").textContent = producto.ubicacion_producto || "-";
    document.getElementById("display_peso_volumetrico").textContent = producto.peso_volumetrico ? `${producto.peso_volumetrico} kg` : "-";
    document.getElementById("display_tipo_mercancia").textContent = producto.tipo_de_mercancia || "-";
    document.getElementById("display_unidades").textContent = producto.unidades_existencia || "-";

    // Mostrar la sección de resultados
    document.getElementById("resultsSection").classList.remove("hidden");

    // Hacer scroll a la sección de resultados
    document.getElementById("resultsSection").scrollIntoView({
        behavior: "smooth",
        block: "nearest"
    });
}

// Función para confirmar y eliminar el producto
function confirmarEliminacionProducto() {
    const idProducto = document.getElementById("id_producto").value;
    const nombreProducto = document.getElementById("display_nombre").textContent;

    if (!idProducto) {
        alerta("Error", "No hay ningún producto seleccionado", "error");
        return;
    }

    // Confirmación con SweetAlert2
    confirmar(
        "¿Eliminar Producto?",
        `¿Está seguro de eliminar el producto "${nombreProducto}"?\n\nEsta acción NO se puede deshacer.`
    )
        .then(r => {
            if (!r.isConfirmed) return;

            // Realizar la petición de eliminación
            apiRequestProductos("eliminar_producto", { id_producto: idProducto })
                .then(r => r.text())
                .then(resp => {
                    if (resp.trim() === "OK") {
                        alerta("Éxito", "Producto eliminado correctamente", "success");
                        limpiarFormularioEliminarProducto();
                    } else {
                        alerta("Error", resp, "error");
                    }
                })
                .catch(err => {
                    console.error("Error al eliminar producto:", err);
                    alerta("Error", "Ocurrió un error al eliminar el producto", "error");
                });
        });
}

// Función para limpiar el formulario de eliminar
function limpiarFormularioEliminarProducto() {
    document.getElementById("producto_input").value = "";
    document.getElementById("id_producto").value = "";
    document.getElementById("resultsSection").classList.add("hidden");
    document.getElementById("sugerencias_eliminar").classList.remove("activo");
    window.productoSeleccionadoEliminar = null;

    // Limpiar valores mostrados
    document.getElementById("display_nombre").textContent = "-";
    document.getElementById("display_ubicacion").textContent = "-";
    document.getElementById("display_peso_volumetrico").textContent = "-";
    document.getElementById("display_tipo_mercancia").textContent = "-";
    document.getElementById("display_unidades").textContent = "-";
}

/********************************************
 *  UTILIDADES GENERALES PARA CRUD (API)
 ********************************************/

/**
 * Hacer peticiones AJAX al backend.
 * Igual que apiRequestUsuarios(), pero versión para productos.
 */
function apiRequestProductos(accion, datos = null) {

    const formData = datos instanceof HTMLFormElement
        ? new FormData(datos)
        : new FormData();

    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const k in datos) {
            if (datos[k] !== undefined && datos[k] !== null) formData.append(k, datos[k]);
        }
    }

    formData.append("action", accion);

    return fetch('/ajax/productos-ajax.php', {
        method: "POST",
        body: formData
    });
}

/**
 * Manejo centralizado de respuestas del backend
 * Igual que manejarRespuestaCRUD() que ya usabas en usuarios/personal.
 */
function manejarRespuestaCRUD(respuesta, mensajeExito, redireccion = null) {

    if (typeof respuesta !== 'string') {
        try {
            const j = JSON.parse(respuesta);
            if (j.ok || j.success || j.status === 'OK') respuesta = "OK";
            else if (j.error) respuesta = j.error;
            else respuesta = JSON.stringify(j);
        } catch (e) {
            // no JSON
        }
    }

    if (respuesta.trim() === "OK") {
        if (typeof alerta === "function") {
            alerta("Éxito", mensajeExito, "success").then(() => {
                if (redireccion) window.location.href = redireccion;
            });
        } else {
            if (redireccion) window.location.href = redireccion;
            else alert(mensajeExito);
        }
    } else {
        if (typeof alerta === "function") alerta("Error", respuesta, "error");
        else alert(respuesta);
    }
}