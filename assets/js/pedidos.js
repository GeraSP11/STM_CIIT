// Estado global
let pedidoSeleccionado = null;
let localidades = [];
const productosPedido = {};


// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    cargarLocalidades();
    inicializarEventos();
});

// Inicializar eventos
function inicializarEventos() {

    // Formulario de registro
    if (document.getElementById('formRegistroProductos')) {
        configurarVistaProductos();
    }

    // Formulario de búsqueda
    document.getElementById('form-busqueda').addEventListener('submit', buscarPedidos);

    // Botón actualizar
    document.getElementById('btn-actualizar').addEventListener('click', irAActualizar);

    // Botón guardar
    document.getElementById('btn-guardar').addEventListener('click', guardarCambios);

    // Navegación breadcrumb
    document.getElementById('breadcrumb-nav').addEventListener('click', manejarNavegacion);
}

// Iniciar la dinamica de vistas entre registro de pedido y lista de productos
function configurarVistaProductos() {
    const btnAgregarProducto = document.getElementById('btnAgregarProducto');
    const btnRegresar = document.getElementById('btnRegresar');
    const inputBuscarProducto = document.getElementById('buscarProducto');

    if (btnAgregarProducto) {
        btnAgregarProducto.addEventListener('click', mostrarVistaProductos);
    }

    if (btnRegresar) {
        btnRegresar.addEventListener('click', mostrarVistaRegistro);
    }

    if (inputBuscarProducto) {
        inputBuscarProducto.addEventListener('input', function () {
            cargarProductos(this.value.trim());
        });
    }
}
// Mostrar u ocultar la seccion de productos
function mostrarVistaProductos() {
    const vistaRegistro = document.getElementById('vista-registro');
    const vistaProductos = document.getElementById('vista-productos');

    if (vistaRegistro && vistaProductos) {
        vistaRegistro.style.display = 'none';
        vistaProductos.style.display = 'block';
        cargarProductos();
        scrollArriba();
        document.getElementById('btnAgregarProductos').addEventListener('click', () => {
            document.getElementById('vista-productos').style.display = 'none';
            document.getElementById('vista-registro').style.display = 'block';

            agregarProductosSeleccionados();
        })
    }
}
// Mostrar u ocultar la seccion de registro
function mostrarVistaRegistro() {
    const vistaRegistro = document.getElementById('vista-registro');
    const vistaProductos = document.getElementById('vista-productos');

    if (vistaRegistro && vistaProductos) {
        vistaProductos.style.display = 'none';
        vistaRegistro.style.display = 'block';
        scrollArriba();
    }
}
function scrollArriba() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
// Agregar productos seleccionados a la vista de registro
function agregarProductosSeleccionados() {

    const checks = document.querySelectorAll('.chk-producto:checked');
    const tbody = document.getElementById('tablaPedido');

    checks.forEach(chk => {

        const fila = chk.closest('tr');
        const id = chk.value;

        if (productosPedido[id]) return; // ya existe

        const nombre = fila.children[1].innerText;
        const peso = fila.children[2].innerText;
        const existencia = chk.dataset.existencia;


        productosPedido[id] = true;

        tbody.insertAdjacentHTML('beforeend', `
            <tr data-id="${id}">
                <td>${nombre}</td>
                <td>
                    <input type="number"
                        class="form-control input-cantidad"
                        min="1"
                        max="${existencia}"
                        value="1"
                        data-max="${existencia}"
                        oninput="validarCantidad(this)">


                </td>
                <td>${peso}</td>
                <td>
                    <input type="text" class="form-control">
                </td>
                <td class="text-center">
                    <i class="fas fa-trash text-danger"
                       style="cursor:pointer"
                       onclick="eliminarProducto(${id})"></i>
                </td>
            </tr>
        `);
    });

    if (Object.keys(productosPedido).length > 0) {
        document.getElementById('contenedorTablaPedido').style.display = 'block';
        mostrarBotonesRegistro();
    }
}
// Eliminar productos del pedido
function eliminarProducto(id) {
    delete productosPedido[id];
    document.querySelector(`tr[data-id="${id}"]`).remove();

    if (Object.keys(productosPedido).length === 0) {
        document.getElementById('contenedorTablaPedido').style.display = 'none';
        ocultarBotonesRegistro();
    }
}
// Mostrar botones de registro
function mostrarBotonesRegistro() {
    document.getElementById('botonesRegistro').style.display = 'block';
}
function ocultarBotonesRegistro() {
    document.getElementById('botonesRegistro').style.display = 'none';
}
// Validar cantidades maximas en los spinners
function validarCantidad(input) {
    const min = 1;
    const max = parseInt(input.dataset.max);
    let valor = parseInt(input.value);

    if (isNaN(valor) || valor < min) {
        input.value = min;
    } else if (valor > max) {
        input.value = max;
    }
}
document.addEventListener('keydown', function (e) {
    if (
        e.target.classList.contains('input-cantidad') &&
        ['e', 'E', '+', '-', '.'].includes(e.key)
    ) {
        e.preventDefault();
    }
});




// Cargar Productos para la vista de seleccion en el registro
function cargarProductos(filtro = '') {

    const formData = new FormData();
    formData.append('accion', 'listarProductos');
    formData.append('busqueda', filtro);

    fetch('/ajax/pedidos-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error(data.message);
                return;
            }
            renderizarTablaProductos(data.data);
        })
        .catch(error => console.error(error));
}
// Renderizar la tabla de productos
function renderizarTablaProductos(productos) {
    const tablaProductosBody = document.getElementById('tablaProductos');
    tablaProductosBody.innerHTML = '';

    if (productos.length === 0) {
        tablaProductosBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No se encontraron productos
                </td>
            </tr>`;
        return;
    }

    productos.forEach(p => {
        tablaProductosBody.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="text-center">
                    <input type="checkbox"
                        class="chk-producto"
                        value="${p.id_producto}"
                        data-existencia="${p.unidades_existencia}">
                </td>
                <td>${p.nombre_producto}</td>
                <td>${p.peso}</td>
                <td>${p.localidad}</td>
            </tr>
        `);
    });

    inicializarEventosCheckbox();
}
function inicializarEventosCheckbox() {
    const checkboxes = document.querySelectorAll('.chk-producto');

    checkboxes.forEach(chk => {
        chk.addEventListener('change', actualizarEstadoBotonAgregar);
    });
}
function actualizarEstadoBotonAgregar() {
    const seleccionados = document.querySelectorAll('.chk-producto:checked');
    const btn = document.getElementById('btnAgregarProductos');

    if (seleccionados.length > 0) {
        btn.disabled = false;
        btn.classList.remove('btn-gris');
        btn.classList.add('btn-activo');
    } else {
        btn.disabled = true;
        btn.classList.add('btn-gris');
        btn.classList.remove('btn-activo');
    }
}

function obtenerProductosSeleccionados() {
    const seleccionados = [];

    document.querySelectorAll('.chk-producto:checked')
        .forEach(chk => seleccionados.push(chk.value));

    return seleccionados;
}

// Buscador de productos
function filtrarProductos(e) {
    cargarProductos(e.target.value.trim());
}




// Cargar localidades para los selects
function cargarLocalidades() {
    const formData = new FormData();
    formData.append('accion', 'obtener_localidades');

    fetch('/ajax/pedidos-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            // Verificar si la respuesta es correcta
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text(); // Primero obtener como texto
        })
        .then(text => {
            console.log('Respuesta del servidor:', text); // Para debug
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    localidades = data.localidades;
                    llenarSelectsLocalidades();
                } else {
                    mostrarAlerta('error', data.message || 'Error al cargar localidades');
                }
            } catch (e) {
                console.error('Error al parsear JSON:', e);
                console.error('Texto recibido:', text);
                mostrarAlerta('error', 'Error al procesar la respuesta del servidor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('error', 'Error de conexión al cargar localidades');
        });
}

// Llenar los selects de localidades
function llenarSelectsLocalidades() {
    const selectOrigen = document.getElementById('localidad-origen');
    const selectDestino = document.getElementById('localidad-destino');

    localidades.forEach(loc => {
        const optionOrigen = document.createElement('option');
        optionOrigen.value = loc.id_localidad;
        optionOrigen.textContent = loc.nombre_completo;
        selectOrigen.appendChild(optionOrigen);

        const optionDestino = document.createElement('option');
        optionDestino.value = loc.id_localidad;
        optionDestino.textContent = loc.nombre_completo;
        selectDestino.appendChild(optionDestino);
    });
}

// Buscar pedidos
function buscarPedidos(e) {
    e.preventDefault();

    const clavePedido = document.getElementById('clave-pedido').value.trim();
    const localidadOrigen = document.getElementById('localidad-origen').value;
    const localidadDestino = document.getElementById('localidad-destino').value;

    // Validar que al menos un campo esté lleno
    if (!clavePedido && !localidadOrigen && !localidadDestino) {
        mostrarAlerta('warning', 'Por favor ingrese al menos un criterio de búsqueda');
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'buscar');
    if (clavePedido) formData.append('clave_pedido', clavePedido);
    if (localidadOrigen) formData.append('localidad_origen', localidadOrigen);
    if (localidadDestino) formData.append('localidad_destino', localidadDestino);

    mostrarLoading(true);

    fetch('/ajax/pedidos-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            mostrarLoading(false);

            if (data.success) {
                if (data.pedidos.length > 0) {
                    mostrarResultados(data.pedidos);
                } else {
                    mostrarAlerta('info', 'No se encontraron pedidos con los criterios especificados');
                }
            } else {
                mostrarAlerta('error', data.message || 'Error al buscar pedidos');
            }
        })
        .catch(error => {
            mostrarLoading(false);
            console.error('Error:', error);
            mostrarAlerta('error', 'Error de conexión al buscar pedidos');
        });
}

// Mostrar resultados
function mostrarResultados(pedidos) {
    // Ocultar vista de búsqueda y mostrar resultados
    document.getElementById('vista-busqueda').style.display = 'none';
    document.getElementById('vista-resultados').style.display = 'block';

    // Actualizar breadcrumb
    actualizarBreadcrumb('resultados');

    // Cargar resultados
    const listaResultados = document.getElementById('lista-resultados');
    listaResultados.innerHTML = '';

    if (pedidos.length === 0) {
        listaResultados.innerHTML = '<div class="mensaje-vacio">No se encontraron pedidos</div>';
        return;
    }

    pedidos.forEach(pedido => {
        const item = document.createElement('div');
        item.className = 'resultado-item';
        item.innerHTML = `
            <input type="checkbox" class="checkbox-resultado" 
                   data-id="${pedido.id_pedido}" 
                   data-clave="${pedido.clave_pedido}">
            <span>ID: ${pedido.id_pedido} &nbsp;&nbsp;&nbsp; 
                  Clave: ${pedido.clave_pedido} &nbsp;&nbsp;&nbsp; 
                  Estatus: ${pedido.estatus_pedido}</span>
        `;
        listaResultados.appendChild(item);

        // Hacer que todo el item sea clickeable
        item.addEventListener('click', function (e) {
            if (e.target.tagName !== 'INPUT') {
                const checkbox = item.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                actualizarSeleccion();
            }
        });
    });

    // Manejar cambios en checkboxes
    document.querySelectorAll('.checkbox-resultado').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarSeleccion);
    });
}

// Actualizar selección
function actualizarSeleccion() {
    const checkboxes = document.querySelectorAll('.checkbox-resultado:checked');

    // Permitir solo una selección
    if (checkboxes.length > 1) {
        checkboxes.forEach((cb, index) => {
            if (index < checkboxes.length - 1) {
                cb.checked = false;
            }
        });
    }

    const seleccionado = document.querySelector('.checkbox-resultado:checked');
    if (seleccionado) {
        pedidoSeleccionado = {
            id: seleccionado.dataset.id,
            clave: seleccionado.dataset.clave
        };
    } else {
        pedidoSeleccionado = null;
    }
}

// Ir a actualizar
function irAActualizar() {
    if (!pedidoSeleccionado) {
        mostrarAlerta('warning', 'Por favor seleccione un pedido');
        return;
    }

    mostrarLoading(true);

    fetch(`/ajax/pedidos-ajax.php?accion=obtener&id=${pedidoSeleccionado.id}`)
        .then(response => response.json())
        .then(data => {
            mostrarLoading(false);

            if (data.success) {
                mostrarVistaActualizar(data.pedido);
            } else {
                mostrarAlerta('error', data.message || 'Error al cargar el pedido');
            }
        })
        .catch(error => {
            mostrarLoading(false);
            console.error('Error:', error);
            mostrarAlerta('error', 'Error de conexión al cargar el pedido');
        });
}

// Mostrar vista actualizar
function mostrarVistaActualizar(pedido) {
    // Ocultar vista de resultados y mostrar vista de actualización
    document.getElementById('vista-resultados').style.display = 'none';
    document.getElementById('vista-actualizar').style.display = 'block';

    // Actualizar breadcrumb
    actualizarBreadcrumb('actualizar');

    // Llenar campos
    document.getElementById('detalle-id').textContent = pedido.id_pedido;
    document.getElementById('detalle-clave').textContent = pedido.clave_pedido;
    document.getElementById('detalle-estatus').value = pedido.estatus_pedido;
    document.getElementById('detalle-fecha-solicitud').value = pedido.fecha_solicitud;
    document.getElementById('detalle-fecha-entrega').value = pedido.fecha_entrega || '';
    document.getElementById('detalle-localidad-origen').textContent = pedido.localidad_origen_nombre;
    document.getElementById('detalle-localidad-destino').textContent = pedido.localidad_destino_nombre;
    document.getElementById('detalle-observaciones').value = pedido.observaciones || '';
}

// Guardar cambios
function guardarCambios() {
    const idPedido = document.getElementById('detalle-id').textContent;
    const estatus = document.getElementById('detalle-estatus').value;
    const fechaSolicitud = document.getElementById('detalle-fecha-solicitud').value;
    const fechaEntrega = document.getElementById('detalle-fecha-entrega').value;
    const observaciones = document.getElementById('detalle-observaciones').value;

    // Validaciones
    if (!estatus || !fechaSolicitud) {
        mostrarAlerta('warning', 'Por favor complete los campos requeridos');
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'actualizar');
    formData.append('id_pedido', idPedido);
    formData.append('estatus_pedido', estatus);
    formData.append('fecha_solicitud', fechaSolicitud);
    formData.append('fecha_entrega', fechaEntrega);
    formData.append('observaciones', observaciones);

    mostrarLoading(true);

    fetch('/ajax/pedidos-ajax.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            mostrarLoading(false);

            if (data.success) {
                mostrarAlerta('success', 'Pedido actualizado exitosamente');
                setTimeout(() => {
                    volverAInicio();
                }, 2000);
            } else {
                mostrarAlerta('error', data.message || 'Error al actualizar el pedido');
            }
        })
        .catch(error => {
            mostrarLoading(false);
            console.error('Error:', error);
            mostrarAlerta('error', 'Error de conexión al actualizar el pedido');
        });
}

// Volver a inicio
function volverAInicio() {
    document.getElementById('vista-busqueda').style.display = 'block';
    document.getElementById('vista-resultados').style.display = 'none';
    document.getElementById('vista-actualizar').style.display = 'none';

    actualizarBreadcrumb('inicio');

    // Limpiar formulario
    document.getElementById('form-busqueda').reset();
    pedidoSeleccionado = null;

    // Limpiar mensaje de alerta
    document.getElementById('mensaje-alerta').innerHTML = '';
}

// Actualizar breadcrumb
function actualizarBreadcrumb(vista) {
    const breadcrumb = document.getElementById('breadcrumb-nav');

    switch (vista) {
        case 'inicio':
            breadcrumb.innerHTML = `
                <li class="breadcrumb-item">
                    <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Consulta de Pedidos
                </li>
            `;
            break;

        case 'resultados':
            breadcrumb.innerHTML = `
                <li class="breadcrumb-item">
                    <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="#" id="link-inicio">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#" id="link-consulta">Consulta de Pedidos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Resultados de búsqueda</li>
            `;
            break;

        case 'actualizar':
            breadcrumb.innerHTML = `
                <li class="breadcrumb-item">
                    <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
                </li>
                <li class="breadcrumb-item"><a href="#" id="link-inicio">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#" id="link-consulta">Consulta de Pedidos</a></li>
                <li class="breadcrumb-item"><a href="#" id="link-resultados">Resultados de búsqueda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Actualizar pedido</li>
            `;
            break;
    }
}

// Manejar navegación
function manejarNavegacion(e) {
    if (e.target.id === 'link-inicio' || e.target.id === 'link-consulta') {
        e.preventDefault();
        volverAInicio();
    } else if (e.target.id === 'link-resultados') {
        e.preventDefault();
        document.getElementById('vista-actualizar').style.display = 'none';
        document.getElementById('vista-resultados').style.display = 'block';
        actualizarBreadcrumb('resultados');
    }
}

// Mostrar alerta
function mostrarAlerta(tipo, mensaje) {
    const alertaDiv = document.getElementById('mensaje-alerta');
    const tipoClase = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };

    alertaDiv.innerHTML = `
        <div class="alert ${tipoClase[tipo]} alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        alertaDiv.innerHTML = '';
    }, 5000);
}

// Mostrar/ocultar loading
function mostrarLoading(mostrar) {
    document.getElementById('loading-overlay').style.display = mostrar ? 'flex' : 'none';
}















// =======================================================
// FUNCIONALIDAD CONSULTAR PEDIDOS
// =======================================================
document.addEventListener('DOMContentLoaded', function () {

    // Referencias a elementos del DOM
    const formConsulta = document.getElementById("formConsulta");

    // Si no existe el formulario en esta vista, salimos
    if (!formConsulta) return;
    cargarLocalidades();
    const inputIdPedido = document.getElementById("clavePedido");
    const inputOrigen = document.getElementById("origen");
    const inputDestino = document.getElementById("destino");
    const divTablaResultados = document.getElementById("tablaResultados");
    const tablaPedidosTbody = document.querySelector("#tablaPedidos tbody");

    // Modal y campos detalle
    const modalPedido = new bootstrap.Modal(document.getElementById("modalPedido"));
    const detalleCampos = {
        id: document.getElementById("detalle-id"),
        estatus: document.getElementById("detalle-estatus"),
        fechaSolicitud: document.getElementById("detalle-fecha-solicitud"),
        fechaEntrega: document.getElementById("detalle-fecha-entrega"),
        producto: document.getElementById("detalle-producto"),
        origen: document.getElementById("detalle-origen"),
        destino: document.getElementById("detalle-destino"),
        cantidad: document.getElementById("detalle-cantidad"),
        unidad: document.getElementById("detalle-unidad"),
        observaciones: document.getElementById("detalle-observaciones")
    };

    // ============================================
    // PASO 1: Listener del formulario de búsqueda
    // ============================================
    formConsulta.addEventListener("submit", function(e) {
        e.preventDefault();
        consultarPedidos();
    });

    // ============================================
    // PASO 2: Función que consulta los pedidos filtrados
    // ============================================
    function consultarPedidos() {

        const filtros = {
            idPedido: inputIdPedido.value.trim(),
            origen: inputOrigen.value.trim(),
            destino: inputDestino.value.trim()
        };

        // Validación: al menos un filtro debe estar lleno
        if (!filtros.idPedido && !filtros.origen && !filtros.destino) {
            alerta("Aviso", "Ingresa al menos un filtro para buscar.", "info");
            return;
        }

        // Mostrar estado de carga
        tablaPedidosTbody.innerHTML = `<tr><td colspan="2" class="text-center">Cargando...</td></tr>`;
        divTablaResultados.style.display = 'block';

        // Llamada al backend
        enviarPeticionPOST("consultar-pedidos", filtros)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`Error HTTP: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log("Respuesta del servidor:", data);
                
                if (data.success && data.pedidos) {
                    // Ocultar formulario de filtros
                    const formContainer = document.querySelector('.form-container');
                    if (formContainer) {
                        formContainer.style.display = 'none';
                    }
                    
                    mostrarResultadosTabla(data.pedidos);
                } else {
                    throw new Error(data.error || "Error desconocido");
                }
            })
            .catch(err => {
                console.error("Error en la consulta:", err);
                tablaPedidosTbody.innerHTML = `<tr><td colspan="2" class="text-center text-danger">Error: ${err.message}</td></tr>`;
                alerta("Error", "No se pudieron obtener los pedidos", "error");
            });
    }

    // ============================================
    // PASO 3: Mostrar resultados en la tabla
    // ============================================
    function mostrarResultadosTabla(pedidos) {
        
        // Si no hay resultados
        if (pedidos.length === 0) {
            tablaPedidosTbody.innerHTML = `<tr><td colspan="2" class="text-center">No se encontraron pedidos</td></tr>`;
            return;
        }

        // Limpiar tabla
        tablaPedidosTbody.innerHTML = '';

        // Llenar tabla con cada pedido
        pedidos.forEach(pedido => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${pedido.clave_pedido || pedido.id_pedido}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="verDetallePedido(${pedido.id_pedido})">
                        <i class="bi bi-eye"></i> Ver detalle
                    </button>
                </td>
            `;
            tablaPedidosTbody.appendChild(fila);
        });
    }

    // ============================================
    // PASO 4: Función global para ver detalle (llamada desde botón)
    // ============================================
    window.verDetallePedido = function(idPedido) {
        console.log("Solicitando detalle del pedido:", idPedido);

        // Llamada al backend para obtener detalle completo
        enviarPeticionPOST("detalle-pedido", { idPedido: idPedido })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`Error HTTP: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log("Detalle recibido:", data);
                
                if (data.success && data.pedido) {
                    mostrarModalPedido(data.pedido, data.detalles);
                } else {
                    throw new Error(data.error || "Error al obtener detalle");
                }
            })
            .catch(err => {
                console.error("Error al obtener detalle:", err);
                alerta("Error", "No se pudo obtener el detalle del pedido", "error");
            });
    };

    // ============================================
    // PASO 5: Mostrar datos en el modal
    // ============================================
    function mostrarModalPedido(pedido, detalles) {
        
        // Llenar información general del pedido
        detalleCampos.id.textContent = pedido.clave_pedido || pedido.id_pedido || "";
        detalleCampos.estatus.textContent = pedido.estatus_pedido || "";
        detalleCampos.fechaSolicitud.textContent = pedido.fecha_solicitud || "";
        detalleCampos.fechaEntrega.textContent = pedido.fecha_entrega || "";
        detalleCampos.origen.textContent = pedido.origen || "";
        detalleCampos.destino.textContent = pedido.destino || "";
        detalleCampos.unidad.textContent = pedido.unidad || "";
        detalleCampos.cantidad.textContent = pedido.cantidad || "";
        detalleCampos.observaciones.textContent = pedido.observaciones || "Sin observaciones";

        // Procesar detalles de productos
        if (detalles && detalles.length > 0) {

            // Vamos a hacer un select donde se va a rellenar con todos los productos.
            // Si hay múltiples productos, mostrar el primero o concatenar
           /* const primerProducto = detalles[0];
            detalleCampos.producto.textContent = primerProducto.producto || "";
            detalleCampos.cantidad.textContent = primerProducto.cantidad || "";
            detalleCampos.unidad.textContent = "unidades"*/
            
            // Si quieres mostrar todos los productos:
            const productos = detalles.map(d => `${d.producto}`);
            detalleCampos.producto.textContent = productos;
        } else {
            detalleCampos.producto.textContent = "Sin productos";
            detalleCampos.cantidad.textContent = "0";
            detalleCampos.unidad.textContent = "-";
        }

        // Mostrar el modal
        modalPedido.show();
    }

    // ============================================
    // Cargar localidades para los selects
    // ============================================
    function cargarLocalidades() {
        enviarPeticionPOST("obtener_localidades")
            .then(res => res.json())
            .then(data => {
                if (data.success && data.localidades) {
                    llenarSelectLocalidades(data.localidades);
                }
            })
            .catch(err => {
                console.error("Error al cargar localidades:", err);
            });
    }

    function llenarSelectLocalidades(localidades) {
        const selectOrigen = document.getElementById('origen');
        const selectDestino = document.getElementById('destino');

        // Limpiar selects antes de llenarlos
        selectOrigen.innerHTML = '<option value="">Seleccione origen</option>';
        selectDestino.innerHTML = '<option value="">Seleccione destino</option>';

        localidades.forEach(loc => {
            const optionOrigen = document.createElement('option');
            optionOrigen.value = loc.nombre_centro_trabajo;
            optionOrigen.textContent = loc.nombre_centro_trabajo;
            selectOrigen.appendChild(optionOrigen);

            const optionDestino = document.createElement('option');
            optionDestino.value = loc.nombre_centro_trabajo;
            optionDestino.textContent = loc.nombre_centro_trabajo;
            selectDestino.appendChild(optionDestino);
        });
    }

});

// ============================================
// Función global para nueva búsqueda
// ============================================
function nuevaBusqueda() {
    // Mostrar formulario
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
        formContainer.style.display = 'block';
    }
    
    // Ocultar tabla
    const divTablaResultados = document.getElementById("tablaResultados");
    if (divTablaResultados) {
        divTablaResultados.style.display = 'none';
    }
    
    // Limpiar formulario
    document.getElementById("formConsulta").reset();
}


// =======================================================
// FUNCIONES AUXILIARES
// =======================================================

/**
 * Envía una petición POST al backend con acción y datos opcionales
 */
function enviarPeticionPOST(accion, datos = null) {

    const formData = datos instanceof HTMLFormElement
        ? new FormData(datos)
        : new FormData();

    if (datos && !(datos instanceof HTMLFormElement)) {
        for (const clave in datos) {
            formData.append(clave, datos[clave]);
        }
    }

    formData.append("accion", accion);

    return fetch('/ajax/pedidos-ajax.php', {
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