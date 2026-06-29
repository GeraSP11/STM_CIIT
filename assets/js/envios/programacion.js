/**
 * programacion.js
 *
 * Lógica de la vista "Solución de transporte":
 *  1) Toma los pedidos elegidos en la vista de selección.
 *  2) Pide al backend (action=generar_problema) que construya orígenes,
 *     destinos, oferta, demanda y la matriz de costos/distancias.
 *  3) Resuelve el problema reutilizando TransporteSolverJS (Esquina
 *     Noroeste + MODI). El resultado se expresa en distancia (km), no en
 *     costos monetarios.
 *  4) Pinta los KPIs, la matriz de asignación y el detalle de distancias por ruta.
 *  5) Al confirmar, llama a action=confirmar_programacion para que el backend
 *     cambie el estatus de los pedidos seleccionados a 'En recolección'.
 */

(function () {
    const AJAX_URL = '/ajax/envios-ajax.php';

    const vistaSeleccion = document.getElementById('vista-seleccion');
    const vistaProgramacion = document.getElementById('vista-programacion');
    const btnProgramar = document.getElementById('btn-programar-envios');
    const btnVolver = document.getElementById('btn-volver-seleccion');
    const btnVolverTexto = document.getElementById('btn-volver-seleccion-texto');
    const btnCancelar = document.getElementById('btn-cancelar-programacion');
    const btnConfirmar = document.getElementById('btn-confirmar-programacion');
    const estadoProgramacion = document.getElementById('estado-programacion');
    const cargando = document.getElementById('cargando-programacion');
    const contenido = document.getElementById('contenido-programacion');
    const resumenPedidos = document.getElementById('resumen-pedidos-seleccionados');
    const tablaResueltaCont = document.getElementById('tabla-problema-resuelto');
    const resultadosCont = document.getElementById('resultados-solucion');

    const kpiTotalUnidades = document.getElementById('kpi-total-unidades');
    const kpiRutasAsignadas = document.getElementById('kpi-rutas-asignadas');
    const kpiCostoTotal = document.getElementById('kpi-costo-total');

    const solver = new TransporteSolverJS();

    /** Última solución calculada, lista para confirmarse. */
    let ultimoResultado = null;
    let ultimosDatos = null;
    let ultimaSeleccion = null;

    function mostrarEstado(contenedor, mensaje, tipo) {
        contenedor.innerHTML = mensaje ? `<div class="alerta alerta--${tipo}">${escaparHtml(mensaje)}</div>` : '';
    }

    function cambiarVista(mostrarProgramacion) {
        vistaSeleccion.classList.toggle('vista--activa', !mostrarProgramacion);
        vistaProgramacion.classList.toggle('vista--activa', mostrarProgramacion);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function iniciarProgramacion() {
        const seleccion = window.PedidosTMS.obtenerSeleccionActual();
        if (seleccion.length < 3) return;

        ultimaSeleccion = seleccion;

        cambiarVista(true);
        contenido.style.display = 'none';
        cargando.classList.add('cargando--activa');
        mostrarEstado(estadoProgramacion, '', null);
        renderizarResumenPedidos(seleccion);

        try {
            const formData = new URLSearchParams();
            formData.append('action', 'generar_problema');
            seleccion.forEach((pedido) => formData.append('pedidos[]', pedido.id_pedido));

            const respuesta = await fetch(AJAX_URL, { method: 'POST', body: formData });
            const json = await respuesta.json();

            if (!json.success) {
                throw new Error(json.message || 'No fue posible generar el problema de transporte.');
            }

            procesarProblema(json.data);
        } catch (error) {
            mostrarEstado(estadoProgramacion, error.message, 'error');
            contenido.style.display = 'none';
        } finally {
            cargando.classList.remove('cargando--activa');
        }
    }

    function renderizarResumenPedidos(seleccion) {
        resumenPedidos.innerHTML = `
            <p class="texto-ayuda">${seleccion.length} pedidos incluidos en esta programación:</p>
            <ul class="lista-pedidos">
                ${seleccion.map((p) => `
                    <li><strong>${escaparHtml(p.clave_pedido)}</strong>:
                        ${escaparHtml(p.nombre_origen)} → ${escaparHtml(p.nombre_destino)}
                    </li>
                `).join('')}
            </ul>
        `;
    }

    function procesarProblema(datos) {
        if (datos.origenes.length === 0 || datos.destinos.length === 0) {
            mostrarEstado(estadoProgramacion, 'No hay orígenes/destinos suficientes para construir el problema.', 'error');
            return;
        }

        const nombresOrigenes = datos.origenes.map((o) => o.nombre);
        const nombresDestinos = datos.destinos.map((d) => d.nombre);

        const resultado = solver.resolver(datos.costos, datos.oferta, datos.demanda, nombresOrigenes, nombresDestinos);

        ultimoResultado = resultado;
        ultimosDatos = datos;

        renderizarKpis(datos, resultado);
        renderizarTablaResuelta(datos, resultado, nombresOrigenes, nombresDestinos);
        renderizarResultados(datos, resultado);

        contenido.style.display = 'block';
    }

    function renderizarKpis(datos, resultado) {
        kpiTotalUnidades.textContent = formatearNumero(resultado.total_oferta);
        kpiRutasAsignadas.textContent = formatearNumero(resultado.envios.length);
        kpiCostoTotal.textContent = `${formatearNumero(resultado.costo_total, 2)} km`;
    }

    function renderizarTablaResuelta(datos, resultado, nombresOrigenes, nombresDestinos) {
        const matrizEnvios = Array.from({ length: nombresOrigenes.length }, () => Array(nombresDestinos.length).fill(0));
        resultado.envios.forEach((envio) => {
            const i = nombresOrigenes.indexOf(envio.origen);
            const j = nombresDestinos.indexOf(envio.destino);
            if (i !== -1 && j !== -1) matrizEnvios[i][j] = envio.cantidad;
        });

        let html = '<table class="tabla-transporte"><thead><tr><th>Origen \\ Destino</th>';
        nombresDestinos.forEach((nombre) => { html += `<th>${escaparHtml(nombre)}</th>`; });
        html += '<th class="celda-oferta-header">Oferta</th></tr></thead><tbody>';

        datos.origenes.forEach((origen, i) => {
            html += `<tr><th>${escaparHtml(origen.nombre)}</th>`;
            datos.destinos.forEach((destino, j) => {
                const cantidad = matrizEnvios[i][j];
                const disponible = datos.rutas_disponibles[i][j];
                const tieneEnvio = cantidad > 0;
                let clase = '';
                if (tieneEnvio) clase = 'celda-asignada';
                else if (!disponible) clase = 'celda-bloqueada';

                html += `<td class="${clase}">
                            ${tieneEnvio ? formatearNumero(cantidad) : (disponible ? '—' : 'Sin ruta')}
                         </td>`;
            });
            html += `<td class="celda-oferta">${formatearNumero(datos.oferta[i])}</td></tr>`;
        });

        html += '<tr class="fila-demanda"><th>Demanda</th>';
        datos.demanda.forEach((demanda) => { html += `<td>${formatearNumero(demanda)}</td>`; });
        html += `<td>${formatearNumero(datos.demanda.reduce((a, b) => a + b, 0))}</td></tr></tbody></table>`;

        tablaResueltaCont.innerHTML = html;
    }

    function renderizarResultados(datos, resultado) {
        const hayCeldaBloqueadaUsada = resultado.envios.some((envio) => {
            const i = datos.origenes.findIndex((o) => o.nombre === envio.origen);
            const j = datos.destinos.findIndex((d) => d.nombre === envio.destino);
            return i !== -1 && j !== -1 && !datos.rutas_disponibles[i][j];
        });

        let html = `
            <div class="alerta alerta--${resultado.balanceado ? 'info' : 'aviso'}">
                Oferta total: ${formatearNumero(resultado.total_oferta)} unidades &nbsp;|&nbsp;
                Demanda total: ${formatearNumero(resultado.total_demanda)} unidades &nbsp;|&nbsp;
                ${resultado.balanceado
                    ? 'Problema balanceado (oferta = demanda).'
                    : 'Problema no balanceado: se agregó un nodo ficticio (dummy) para equilibrar oferta y demanda.'}
                &nbsp;|&nbsp; Iteraciones MODI: ${resultado.estadisticas.iteraciones_modi}
            </div>
            ${hayCeldaBloqueadaUsada ? `
                <div class="alerta alerta--error">
                    Atención: la solución óptima tuvo que asignar unidades sobre al menos una combinación
                    origen-destino que no tiene ninguna ruta registrada en el sistema. Verifica que existan
                    rutas suficientes entre las localidades de los pedidos seleccionados.
                </div>` : ''}
            <div class="tabla-scroll">
                <table class="tabla-transporte">
                    <thead>
                        <tr>
                            <th>Ruta</th><th>Unidades</th><th>Distancia (km)</th><th>Distancia ponderada (km)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${resultado.envios.map((envio) => `
                            <tr>
                                <td style="text-align:left;">${escaparHtml(envio.origen)} → ${escaparHtml(envio.destino)}</td>
                                <td><strong>${formatearNumero(envio.cantidad)}</strong></td>
                                <td>${formatearNumero(envio.costo_unitario)}</td>
                                <td>${formatearNumero(envio.costo_total, 2)}</td>
                            </tr>
                        `).join('')}
                        <tr class="fila-demanda">
                            <td style="text-align:left;"><strong>Total general</strong></td>
                            <td><strong>${formatearNumero(resultado.envios.reduce((acc, e) => acc + e.cantidad, 0))}</strong></td>
                            <td>—</td>
                            <td><strong>${formatearNumero(resultado.costo_total, 2)} km</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;

        resultadosCont.innerHTML = html;
    }

    async function confirmarProgramacion() {
        if (!ultimoResultado || !ultimaSeleccion) return;

        btnConfirmar.disabled = true;
        btnCancelar.disabled = true;
        mostrarEstado(estadoProgramacion, '', null);

        try {
            const formData = new URLSearchParams();
            formData.append('action', 'confirmar_programacion');
            ultimaSeleccion.forEach((pedido) => formData.append('pedidos[]', pedido.id_pedido));

            const respuesta = await fetch(AJAX_URL, { method: 'POST', body: formData });
            const json = await respuesta.json();

            if (!json.success) {
                throw new Error(json.message || 'No fue posible confirmar la programación de envíos.');
            }

            mostrarMensajeExito();
        } catch (error) {
            mostrarEstado(estadoProgramacion, error.message, 'error');
        } finally {
            btnConfirmar.disabled = false;
            btnCancelar.disabled = false;
        }
    }

    function mostrarMensajeExito() {
        const alVolver = () => {
            window.PedidosTMS.limpiarSeleccion();
            window.PedidosTMS.recargarPedidos();
            cambiarVista(false);
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Pedido confirmado exitosamente!',
                text: 'La solución de transporte ha sido registrada y los pedidos pasaron a estatus ' +
                    '"En recolección". Puedes dar seguimiento al pedido desde el panel de control.',
                confirmButtonColor: '#6A0025',
            }).then(alVolver);
        } else {
            alert('¡Pedido confirmado exitosamente! Los pedidos pasaron a estatus "En recolección".');
            alVolver();
        }
    }

    function cancelarProgramacion() {
        cambiarVista(false);
    }

    btnProgramar.addEventListener('click', iniciarProgramacion);
    btnVolver.addEventListener('click', (evento) => { evento.preventDefault(); cambiarVista(false); });
    btnVolverTexto.addEventListener('click', (evento) => { evento.preventDefault(); cambiarVista(false); });
    btnCancelar.addEventListener('click', cancelarProgramacion);
    btnConfirmar.addEventListener('click', confirmarProgramacion);
})();
