/**
 * pedidos.js
 *
 * Lógica de la vista "Selección de pedidos":
 *  - Filtra pedidos en preparación según el selector "Filtrar:":
 *      hoy            -> pedidos con fecha_solicitud = hoy.
 *      semana         -> pedidos con fecha_solicitud dentro de la semana actual.
 *      personalizado  -> el usuario indica fecha de inicio / fecha de fin.
 *  - El backend SIEMPRE filtra adicionalmente por estatus_pedido = 'En preparación';
 *    el frontend no necesita repetir ese filtro porque la API ya lo garantiza.
 *  - Permite marcar pedidos con checkbox.
 *  - Habilita el botón "Configurar carga" cuando hay 3 o más seleccionados.
 *
 * Expone window.PedidosTMS.obtenerSeleccionActual() para que
 * programacion.js pueda leer los pedidos elegidos.
 */

(function () {
    const AJAX_URL = '/ajax/envios-ajax.php';

    const selectTipoFiltro = document.getElementById('select-tipo-filtro');
    const panelRangoPersonalizado = document.getElementById('panel-rango-personalizado');
    const formFiltroFechas = document.getElementById('form-filtro-fechas');
    const pillRangoActivo = document.getElementById('pill-rango-activo');
    const textoConteoRango = document.getElementById('texto-conteo-rango');

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

    function mostrarEstado(contenedor, mensaje, tipo) {
        contenedor.innerHTML = mensaje ? `<div class="alerta alerta--${tipo}">${escaparHtml(mensaje)}</div>` : '';
    }

    function actualizarContador() {
        const total = seleccionados.size;
        contadorSeleccion.textContent = `${total} pedido${total === 1 ? '' : 's'} seleccionado${total === 1 ? '' : 's'} (mínimo 3)`;
        contadorSeleccion.classList.toggle('contador--listo', total >= 3);
        btnProgramar.disabled = total < 3;
    }

    function renderizarPedidos(pedidos) {
        pedidosCargados = pedidos;

        if (pedidos.length === 0) {
            cuerpoTabla.innerHTML = `
                <tr><td colspan="6" class="celda-vacia">
                    No hay pedidos en preparación en el periodo seleccionado.
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
                    <td>${formatearNumero(pedido.total_unidades)} productos</td>
                    <td>${formatearFecha(pedido.fecha_solicitud)}</td>
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

    function actualizarPillRango(tipoFiltro, rango) {
        if (tipoFiltro !== 'personalizado' || !rango) {
            pillRangoActivo.classList.add('d-none');
            pillRangoActivo.textContent = '';
            return;
        }
        pillRangoActivo.textContent = `${formatearFechaCorta(rango.fecha_inicio)} - ${formatearFechaCorta(rango.fecha_fin)}`;
        pillRangoActivo.classList.remove('d-none');
    }

    function actualizarTextoConteo(tipoFiltro, totalPedidos) {
        if (tipoFiltro !== 'personalizado') {
            textoConteoRango.classList.add('d-none');
            return;
        }
        textoConteoRango.textContent = `${totalPedidos} pedido${totalPedidos === 1 ? '' : 's'} en el rango seleccionado`;
        textoConteoRango.classList.remove('d-none');
    }

    function formatearFechaCorta(valorISO) {
        if (!valorISO) return 'dd/mm/aa';
        const [anio, mes, dia] = valorISO.split('-');
        return `${dia}/${mes}/${anio.slice(2)}`;
    }

    async function buscarPedidos() {
        const tipoFiltro = selectTipoFiltro.value;
        const params = new URLSearchParams({ action: 'listar_pedidos', tipo_filtro: tipoFiltro });

        if (tipoFiltro === 'personalizado') {
            const fechaInicio = document.getElementById('fecha_inicio').value;
            const fechaFin = document.getElementById('fecha_fin').value;

            if (!fechaInicio || !fechaFin) {
                mostrarEstado(estadoPedidos, 'Selecciona un rango de fechas completo.', 'error');
                return;
            }
            if (fechaInicio > fechaFin) {
                mostrarEstado(estadoPedidos, 'La fecha de inicio no puede ser posterior a la fecha de fin.', 'error');
                return;
            }

            params.append('fecha_inicio', fechaInicio);
            params.append('fecha_fin', fechaFin);
        }

        mostrarEstado(estadoPedidos, 'Buscando pedidos en preparación…', 'info');
        cuerpoTabla.innerHTML = '';

        try {
            const respuesta = await fetch(`${AJAX_URL}?${params.toString()}`);
            const json = await respuesta.json();

            if (!json.success) {
                throw new Error(json.message || 'No fue posible obtener los pedidos.');
            }

            mostrarEstado(estadoPedidos, '', null);
            renderizarPedidos(json.data);
            actualizarPillRango(tipoFiltro, json.rango);
            actualizarTextoConteo(tipoFiltro, json.data.length);
        } catch (error) {
            mostrarEstado(estadoPedidos, error.message, 'error');
            actualizarTextoConteo(tipoFiltro, 0);
        }
    }

    function onCambiarTipoFiltro() {
        const tipoFiltro = selectTipoFiltro.value;
        const esPersonalizado = tipoFiltro === 'personalizado';

        panelRangoPersonalizado.classList.toggle('d-none', !esPersonalizado);

        if (!esPersonalizado) {
            // "Hoy" y "Esta semana" se calculan en el servidor y se aplican de inmediato.
            buscarPedidos();
        } else {
            // Al entrar a "Rango personalizado" no se busca hasta que el usuario aplique el rango.
            pillRangoActivo.classList.add('d-none');
            textoConteoRango.classList.add('d-none');
        }
    }

    function onSubmitRangoPersonalizado(evento) {
        evento.preventDefault();
        buscarPedidos();
    }

    function inicializarFechasPersonalizado() {
        const inputFin = document.getElementById('fecha_fin');
        const inputInicio = document.getElementById('fecha_inicio');
        if (!inputFin.value) inputFin.value = hoyISO();
        if (!inputInicio.value) {
            const hace7Dias = new Date();
            hace7Dias.setDate(hace7Dias.getDate() - 7);
            inputInicio.value = hace7Dias.toISOString().slice(0, 10);
        }
    }

    function obtenerSeleccionActual() {
        return Array.from(seleccionados.values());
    }

    function limpiarSeleccion() {
        seleccionados.clear();
        actualizarContador();
    }

    function recargarPedidos() {
        return buscarPedidos();
    }

    // API expuesta a programacion.js
    window.PedidosTMS = { obtenerSeleccionActual, limpiarSeleccion, recargarPedidos };

    selectTipoFiltro.addEventListener('change', onCambiarTipoFiltro);
    formFiltroFechas.addEventListener('submit', onSubmitRangoPersonalizado);

    inicializarFechasPersonalizado();
    buscarPedidos(); // Carga inicial: filtro por defecto "Hoy".
})();
