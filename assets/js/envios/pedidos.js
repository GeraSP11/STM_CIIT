/**
 * pedidos.js
 *
 * Lógica de la vista "Selección de pedidos":
 *  - Filtra pedidos por rango de fechas vía AJAX (action=listar_pedidos).
 *  - Permite marcar pedidos con checkbox.
 *  - Habilita el botón "Programar envíos" cuando hay 3 o más seleccionados.
 *
 * Expone window.PedidosTMS.obtenerSeleccionActual() para que
 * programacion.js pueda leer los pedidos elegidos.
 */

(function () {
    const AJAX_URL = '/ajax/envios-ajax.php';

    const form = document.getElementById('form-filtro-fechas');
    const cuerpoTabla = document.getElementById('cuerpo-tabla-pedidos');
    const estadoPedidos = document.getElementById('estado-pedidos');
    const contadorSeleccion = document.getElementById('contador-seleccion');
    const btnProgramar = document.getElementById('btn-programar-envios');

    let pedidosCargados = [];
    /** Map<string id_pedido, objeto pedido> — conserva selección entre búsquedas. */
    const seleccionados = new Map();

    function hoyISO() {
        return new Date().toISOString().slice(0, 10);
    }

    function inicializarFechas() {
        const inputFin = document.getElementById('fecha_fin');
        const inputInicio = document.getElementById('fecha_inicio');
        if (!inputFin.value) inputFin.value = hoyISO();
        if (!inputInicio.value) {
            const hace30Dias = new Date();
            hace30Dias.setDate(hace30Dias.getDate() - 30);
            inputInicio.value = hace30Dias.toISOString().slice(0, 10);
        }
    }

    function mostrarEstado(contenedor, mensaje, tipo) {
        contenedor.innerHTML = mensaje ? `<div class="alerta alerta--${tipo}">${escaparHtml(mensaje)}</div>` : '';
    }

    function actualizarContador() {
        const total = seleccionados.size;
        contadorSeleccion.textContent = `${total} pedido${total === 1 ? '' : 's'} seleccionado${total === 1 ? '' : 's'} (mínimo 3)`;
        contadorSeleccion.classList.toggle('contador--listo', total >= 3);
        btnProgramar.disabled = total < 3;
    }

    function claseEstatus(estatus) {
        const mapa = {
            'En captura': 'neutro',
            'En preparación': 'info',
            'En recolección': 'info',
            'Enviado': 'aviso',
            'En tránsito': 'aviso',
            'En reparto': 'aviso',
            'Entregado': 'exito',
        };
        return mapa[estatus] || 'neutro';
    }

    function renderizarPedidos(pedidos) {
        pedidosCargados = pedidos;

        if (pedidos.length === 0) {
            cuerpoTabla.innerHTML = `
                <tr><td colspan="8" class="celda-vacia">
                    No se encontraron pedidos registrados en el rango de fechas indicado.
                </td></tr>`;
            return;
        }

        cuerpoTabla.innerHTML = pedidos.map((pedido) => {
            const marcado = seleccionados.has(String(pedido.id_pedido));
            return `
                <tr>
                    <td><input type="checkbox" class="chk-pedido" value="${pedido.id_pedido}" ${marcado ? 'checked' : ''}></td>
                    <td>${escaparHtml(pedido.clave_pedido)}</td>
                    <td>${escaparHtml(pedido.nombre_origen)}</td>
                    <td>${escaparHtml(pedido.nombre_destino)}</td>
                    <td>${formatearFecha(pedido.fecha_solicitud)}</td>
                    <td>${formatearFecha(pedido.fecha_entrega)}</td>
                    <td><span class="badge badge--${claseEstatus(pedido.estatus_pedido)}">${escaparHtml(pedido.estatus_pedido)}</span></td>
                    <td>${formatearNumero(pedido.total_unidades)}</td>
                </tr>
            `;
        }).join('');

        cuerpoTabla.querySelectorAll('.chk-pedido').forEach((checkbox) => {
            checkbox.addEventListener('change', onCambiarSeleccion);
        });
    }

    function onCambiarSeleccion(evento) {
        const id = evento.target.value;
        if (evento.target.checked) {
            const pedido = pedidosCargados.find((p) => String(p.id_pedido) === id);
            if (pedido) seleccionados.set(id, pedido);
        } else {
            seleccionados.delete(id);
        }
        actualizarContador();
    }

    async function buscarPedidos(evento) {
        evento.preventDefault();

        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        if (!fechaInicio || !fechaFin) {
            mostrarEstado(estadoPedidos, 'Selecciona un rango de fechas completo.', 'error');
            return;
        }
        if (fechaInicio > fechaFin) {
            mostrarEstado(estadoPedidos, 'La fecha "Desde" no puede ser posterior a la fecha "Hasta".', 'error');
            return;
        }

        mostrarEstado(estadoPedidos, 'Buscando pedidos…', 'info');
        cuerpoTabla.innerHTML = '';

        try {
            const params = new URLSearchParams({
                action: 'listar_pedidos',
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
            });
            const respuesta = await fetch(`${AJAX_URL}?${params.toString()}`);
            const json = await respuesta.json();

            if (!json.success) {
                throw new Error(json.message || 'No fue posible obtener los pedidos.');
            }

            mostrarEstado(estadoPedidos, '', null);
            renderizarPedidos(json.data);
        } catch (error) {
            mostrarEstado(estadoPedidos, error.message, 'error');
        }
    }

    function obtenerSeleccionActual() {
        return Array.from(seleccionados.values());
    }

    // API expuesta a programacion.js
    window.PedidosTMS = { obtenerSeleccionActual };

    form.addEventListener('submit', buscarPedidos);
    inicializarFechas();
})();
