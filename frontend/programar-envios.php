<?php

require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS", "Operador Logístico", "Jefe de Almacén"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Programación de Envíos';
$seccion = 'Listado de pedidos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/transporte.css">
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="breadcrumb-container mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <h2><?php echo $titulo_seccion; ?></h2>

    <main class="content-area">

        <!-- ================= VISTA 1: SELECCIÓN DE PEDIDOS ================= -->
        <section id="vista-seleccion" class="vista vista--activa">

            <div class="form-section filtro-section">
                <label for="select-tipo-filtro" class="filtro-label">Filtrar:</label>

                <select id="select-tipo-filtro" class="form-select select-filtro">
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta semana</option>
                    <option value="personalizado">Rango personalizado</option>
                </select>

                <!-- Panel de rango personalizado (solo visible si se elige esa opción) -->
                <div id="panel-rango-personalizado" class="panel-rango d-none">
                    <div class="panel-rango__titulo">
                        <i class="fas fa-calendar-alt"></i>
                        Seleccione el rango de fechas
                    </div>
                    <form id="form-filtro-fechas" class="form-row filtro-fechas">
                        <div class="form-col">
                            <label for="fecha_inicio">Fecha de inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="form-col">
                            <label for="fecha_fin">Fecha de fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="form-col form-col--accion">
                            <button type="submit" class="btn-custom">Aplicar rango</button>
                        </div>
                    </form>
                </div>

                <!-- Pill que muestra el rango de fechas activo cuando es personalizado -->
                <div id="pill-rango-activo" class="pill-rango d-none"></div>
            </div>

            <div id="estado-pedidos"></div>

            <div class="tabla-encabezado">
                <span id="contador-seleccion" class="contador">0 pedidos seleccionados (mínimo 3)</span>
            </div>

            <div class="tabla-scroll">
                <table class="table tabla-datos" id="tabla-pedidos">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Pedido</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-tabla-pedidos">
                        <tr>
                            <td colspan="6" class="celda-vacia">Cargando pedidos en preparación…</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p id="texto-conteo-rango" class="texto-conteo d-none"></p>

            <div class="text-end mt-4">
                <button id="btn-programar-envios" class="btn-custom" disabled>
                    Configurar carga <i class="fas fa-chevron-right"></i>
                </button>
            </div>

        </section>

        <!-- ================= VISTA 2: PROGRAMACIÓN DE ENVÍOS (SOLUCIÓN DE TRANSPORTE) ================= -->
        <section id="vista-programacion" class="vista">

            <nav aria-label="breadcrumb" class="breadcrumb-container breadcrumb-container--interna">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#" id="btn-volver-seleccion"><i class="fas fa-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#" id="btn-volver-seleccion-texto">Pedidos a programar</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Solución de transporte</li>
                </ol>
            </nav>

            <div id="resumen-pedidos-seleccionados"></div>

            <div id="estado-programacion"></div>

            <div id="cargando-programacion" class="cargando">
                <div class="spinner"></div>
                <p>Calculando la solución óptima…</p>
            </div>

            <div id="contenido-programacion" class="contenido-programacion">

                <div class="resumen-kpis">
                    <div class="kpi-card">
                        <span class="kpi-card__label">Total unidades</span>
                        <span class="kpi-card__valor" id="kpi-total-unidades">—</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-card__label">Rutas asignadas</span>
                        <span class="kpi-card__valor" id="kpi-rutas-asignadas">—</span>
                    </div>
                    <div class="kpi-card">
                        <span class="kpi-card__label">Distancia total</span>
                        <span class="kpi-card__valor" id="kpi-costo-total">—</span>
                    </div>
                    <div class="kpi-card kpi-card--acento">
                        <span class="kpi-card__label">Distancia óptima</span>
                        <span class="kpi-card__valor">Mínima</span>
                    </div>
                </div>

                <div class="form-section">
                    <div class="seccion-titulo">Matriz de asignación — unidades por ruta (origen → destino)</div>
                    <div class="tabla-scroll" id="tabla-problema-resuelto"></div>
                </div>

                <div class="form-section">
                    <div class="seccion-titulo">Detalle de distancias por ruta</div>
                    <div id="resultados-solucion"></div>
                </div>

                <div class="text-end mt-4">
                    <button id="btn-cancelar-programacion" class="btn-custom btn-custom--secundario">Cancelar</button>
                    <button id="btn-confirmar-programacion" class="btn-custom">Confirmar y realizar</button>
                </div>

            </div>

        </section>

    </main>

    <!-- Bootstrap JS local -->
    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- SCRIPTS DEL MÓDULO DE PROGRAMACIÓN DE ENVÍOS -->
    <script src="/assets/js/envios/utilidades.js"></script>
    <script src="/assets/js/envios/transporte-solver.js"></script>
    <script src="/assets/js/envios/pedidos.js"></script>
    <script src="/assets/js/envios/programacion.js"></script>

</body>

</html>
