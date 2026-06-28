/**
 * programacion.js
 *
 * Lógica de la vista "Programación de envíos":
 *  1) Toma los pedidos elegidos en la vista de selección.
 *  2) Pide al backend (action=generar_problema) que construya orígenes,
 *     destinos, oferta, demanda y la matriz de costos/distancias.
 *  3) Pinta la tabla del problema ORIGINAL.
 *  4) Resuelve el problema reutilizando TransporteSolverJS (Esquina
 *     Noroeste + MODI) y pinta la tabla RESUELTA + resultados.
 */

(function () {
    const AJAX_URL = '/ajax/envios-ajax.php';

    const vistaSeleccion = document.getElementById('vista-seleccion');
    const vistaProgramacion = document.getElementById('vista-programacion');
    const btnProgramar = document.getElementById('btn-programar-envios');
    const btnVolver = document.getElementById('btn-volver-seleccion');
    const estadoProgramacion = document.getElementById('estado-programacion');
    const cargando = document.getElementById('cargando-programacion');
    const contenido = document.getElementById('contenido-programacion');
    const resumenPedidos = document.getElementById('resumen-pedidos-seleccionados');
    const tablaOriginalCont = document.getElementById('tabla-problema-original');
    const tablaResueltaCont = document.getElementById('tabla-problema-resuelto');
    const resultadosCont = document.getElementById('resultados-solucion');

    const solver = new TransporteSolverJS();

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

        renderizarTablaOriginal(datos, nombresDestinos);

        const resultado = solver.resolver(datos.costos, datos.oferta, datos.demanda, nombresOrigenes, nombresDestinos);

        renderizarTablaResuelta(datos, resultado, nombresOrigenes, nombresDestinos);
        renderizarResultados(datos, resultado);

        contenido.style.display = 'block';
    }

    function renderizarTablaOriginal(datos, nombresDestinos) {
        let html = '<table class="tabla-transporte"><thead><tr><th>Origen \\ Destino</th>';
        nombresDestinos.forEach((nombre) => { html += `<th>${escaparHtml(nombre)}</th>`; });
        html += '<th>Oferta</th></tr></thead><tbody>';

        datos.origenes.forEach((origen, i) => {
            html += `<tr><th>${escaparHtml(origen.nombre)}</th>`;
            datos.destinos.forEach((destino, j) => {
                const disponible = datos.rutas_disponibles[i][j];
                const costo = datos.costos[i][j];
                html += `<td class="${disponible ? '' : 'celda-bloqueada'}">
                            ${disponible ? `${formatearNumero(costo)} km` : 'Sin ruta'}
                          </td>`;
            });
            html += `<td class="celda-oferta">${formatearNumero(datos.oferta[i])}</td></tr>`;
        });

        html += '<tr class="fila-demanda"><th>Demanda</th>';
        datos.demanda.forEach((demanda) => { html += `<td>${formatearNumero(demanda)}</td>`; });
        html += '<td></td></tr></tbody></table>';

        tablaOriginalCont.innerHTML = html;
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
        html += '<th>Oferta</th></tr></thead><tbody>';

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
                            <div class="celda-costo">${disponible ? `${formatearNumero(datos.costos[i][j])} km` : 'Sin ruta'}</div>
                            <div class="celda-cantidad">${tieneEnvio ? `📦 ${formatearNumero(cantidad)}` : '-'}</div>
                         </td>`;
            });
            html += `<td class="celda-oferta">${formatearNumero(datos.oferta[i])}</td></tr>`;
        });

        html += '<tr class="fila-demanda"><th>Demanda</th>';
        datos.demanda.forEach((demanda) => { html += `<td>${formatearNumero(demanda)}</td>`; });
        html += '<td></td></tr></tbody></table>';

        tablaResueltaCont.innerHTML = html;
    }

    function renderizarResultados(datos, resultado) {
        const hayCeldaBloqueadaUsada = resultado.envios.some((envio) => {
            const i = datos.origenes.findIndex((o) => o.nombre === envio.origen);
            const j = datos.destinos.findIndex((d) => d.nombre === envio.destino);
            return i !== -1 && j !== -1 && !datos.rutas_disponibles[i][j];
        });

        let html = `
            <div class="costo-total">
                Distancia total ponderada de la solución: ${formatearNumero(resultado.costo_total, 2)} km
            </div>
            <div class="alerta alerta--${resultado.balanceado ? 'info' : 'aviso'}">
                Oferta total: ${formatearNumero(resultado.total_oferta)} unidades &nbsp;|&nbsp;
                Demanda total: ${formatearNumero(resultado.total_demanda)} unidades &nbsp;|&nbsp;
                ${resultado.balanceado
                    ? 'Problema balanceado (oferta = demanda).'
                    : 'Problema no balanceado: se agregó un nodo ficticio (dummy) para equilibrar oferta y demanda.'}
                &nbsp;|&nbsp; Envíos en la solución: ${resultado.estadisticas.numero_envios}
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
                            <th>Origen</th><th>Destino</th><th>Distancia (km)</th>
                            <th>Unidades asignadas</th><th>Distancia ponderada (km)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${resultado.envios.map((envio) => `
                            <tr>
                                <td>${escaparHtml(envio.origen)}</td>
                                <td>${escaparHtml(envio.destino)}</td>
                                <td>${formatearNumero(envio.costo_unitario)}</td>
                                <td><strong>${formatearNumero(envio.cantidad)}</strong></td>
                                <td>${formatearNumero(envio.costo_total, 2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        resultadosCont.innerHTML = html;
    }

    btnProgramar.addEventListener('click', iniciarProgramacion);
    btnVolver.addEventListener('click', () => cambiarVista(false));
})();
