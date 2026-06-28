<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programación de Envíos · CIIT TMS</title>
    <link rel="stylesheet" href="/assets/css/transporte.css">
</head>
<body>

    <header class="app-header">
        <h1 class="app-header__titulo">Programación de Envíos</h1>
        <p class="app-header__subtitulo">
            Selecciona los pedidos a programar y genera automáticamente el problema de
            transporte (oferta/demanda y distancias entre localidades), resuelto con el
            método de la Esquina Noroeste + MODI.
        </p>
    </header>

    <main class="app-main">

        <!-- ================= VISTA 1: SELECCIÓN DE PEDIDOS ================= -->
        <section id="vista-seleccion" class="vista vista--activa">

            <div class="panel">
                <h2>Filtrar pedidos por fecha</h2>
                <p class="texto-ayuda">Se filtra por la fecha de solicitud del pedido.</p>
                <form id="form-filtro-fechas" class="filtro-fechas">
                    <div class="campo">
                        <label for="fecha_inicio">Desde</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="campo">
                        <label for="fecha_fin">Hasta</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <button type="submit" class="btn btn--primario">Buscar pedidos</button>
                </form>
            </div>

            <div class="panel">
                <div class="panel__encabezado">
                    <h2>Pedidos registrados</h2>
                    <span id="contador-seleccion" class="contador">0 pedidos seleccionados (mínimo 3)</span>
                </div>

                <div id="estado-pedidos"></div>

                <div class="tabla-scroll">
                    <table class="tabla-datos" id="tabla-pedidos">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Clave de pedido</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Fecha solicitud</th>
                                <th>Fecha entrega</th>
                                <th>Estatus</th>
                                <th>Unidades</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-pedidos">
                            <tr><td colspan="8" class="celda-vacia">Usa el filtro de fechas para buscar pedidos.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="panel__pie">
                    <button id="btn-programar-envios" class="btn btn--exito" disabled>
                        Programar envíos →
                    </button>
                </div>
            </div>

        </section>

        <!-- ================= VISTA 2: PROGRAMACIÓN DE ENVÍOS ================= -->
        <section id="vista-programacion" class="vista">

            <div class="panel">
                <button id="btn-volver-seleccion" class="btn btn--secundario">← Volver a selección de pedidos</button>
                <h2 style="margin-top:16px;">Pedidos incluidos en esta programación</h2>
                <div id="resumen-pedidos-seleccionados"></div>
            </div>

            <div id="estado-programacion"></div>

            <div id="cargando-programacion" class="cargando">
                <div class="spinner"></div>
                <p>Calculando la solución óptima…</p>
            </div>

            <div id="contenido-programacion" class="contenido-programacion">

                <div class="panel">
                    <h2>Problema de transporte original</h2>
                    <p class="texto-ayuda">
                        Orígenes/destinos: localidades de los pedidos seleccionados · Oferta: existencias de
                        producto en cada origen · Demanda: unidades solicitadas en los pedidos por destino ·
                        Costo de cada celda: distancia máxima (km) registrada en "rutas" entre ese origen y
                        ese destino.
                    </p>
                    <div class="tabla-scroll" id="tabla-problema-original"></div>
                </div>

                <div class="panel">
                    <h2>Problema de transporte resuelto</h2>
                    <p class="texto-ayuda">Asignación óptima de unidades por celda origen-destino.</p>
                    <div class="tabla-scroll" id="tabla-problema-resuelto"></div>
                </div>

                <div class="panel">
                    <h2>Resultados de la solución</h2>
                    <div id="resultados-solucion"></div>
                </div>

            </div>

        </section>

    </main>

    <script src="/assets/js/envios/utilidades.js"></script>
    <script src="/assets/js/envios/transporte-solver.js"></script>
    <script src="/assets/js/envios/pedidos.js"></script>
    <script src="/assets/js/envios/programacion.js"></script>
</body>
</html>
