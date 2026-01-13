// Estado global
let pedidoSeleccionado = null;
let localidades = [];

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarLocalidades();
    inicializarEventos();
});

// Inicializar eventos
function inicializarEventos() {

    // Formulrio de registro
    //document.getElementById('form-registro').addEventListener('submit', registrarPedido);

    // Formulario de búsqueda
    document.getElementById('form-busqueda').addEventListener('submit', buscarPedidos);
    
    // Botón actualizar
    document.getElementById('btn-actualizar').addEventListener('click', irAActualizar);
    
    // Botón guardar
    document.getElementById('btn-guardar').addEventListener('click', guardarCambios);
    
    // Navegación breadcrumb
    document.getElementById('breadcrumb-nav').addEventListener('click', manejarNavegacion);
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
        item.addEventListener('click', function(e) {
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
    
    switch(vista) {
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