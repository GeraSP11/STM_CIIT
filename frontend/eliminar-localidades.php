<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Localidades</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ==========================
           RESET Y ESTILOS GENERALES
           ========================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F5F5F5;
            color: #000000;
            min-height: 100vh;
        }

        /* ==========================
           HEADER PRINCIPAL
           ========================== */
        .main-header {
            background: #541C33;
            color: #FFFFFF;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-header h1 {
            font-size: 1.5rem;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .logo {
            width: 65px;
            height: 65px;
            background: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #541C33;
            font-size: 0.65rem;
            text-align: center;
            line-height: 1.1;
            padding: 8px;
        }

        /* ==========================
           BREADCRUMB
           ========================== */
        .breadcrumb {
            background: #FFFFFF;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .breadcrumb .home-icon {
            color: #8B4513;
            text-decoration: none;
            font-size: 1.2rem;
        }

        .breadcrumb .separator {
            color: #000000;
            margin: 0 0.3rem;
        }

        .breadcrumb span {
            color: #000000;
        }

        /* ==========================
           SUBHEADER
           ========================== */
        .subheader {
            background: #FFFFFF;
            padding: 3rem 2rem;
            text-align: center;
            border-bottom: 1px solid #E0E0E0;
        }

        .subheader h2 {
            font-size: 1.6rem;
            font-weight: 400;
            color: #843409;
            letter-spacing: 0.5px;
        }

        /* ==========================
           CONTENEDOR PRINCIPAL
           ========================== */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        /* ==========================
           SECCIÓN DE BÚSQUEDA
           ========================== */
        .search-section {
            padding: 4rem 2rem;
            background: #F5F5F5;
            margin-bottom: 0;
            display: flex;
            justify-content: center;
        }

        .search-wrapper {
            background: #D9D9D9;
            padding: 2.5rem 3rem;
            max-width: 480px;
            width: 100%;
        }

        /* ==========================
           GRUPOS DE FORMULARIO
           ========================== */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 500;
            color: #FFFFFF;
            font-size: 0.9rem;
            background: #541C33;
            padding: 0.6rem 1rem;
        }

        .required {
            color: #FFFFFF;
            font-weight: 700;
        }

        .helper-text {
            display: block;
            font-size: 0.8rem;
            color: #666666;
            margin-top: 0.6rem;
            text-align: center;
        }

        /* ==========================
           CAMPOS DEL FORMULARIO
           ========================== */
        select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 2px solid #949494;
            border-radius: 0;
            font-size: 0.95rem;
            background: #FFFFFF;
            color: #7E7B7B;
            font-family: inherit;
            transition: border-color 0.3s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237E7B7B' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        select:focus {
            outline: none;
            border-color: #541C33;
        }

        select:disabled {
            background-color: #E8E8E8;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* ==========================
           BOTONES
           ========================== */
        .btn {
            padding: 0.7rem 2.5rem;
            border: none;
            border-radius: 0;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            font-family: inherit;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-primary {
            background: #541C33;
            color: #FFFFFF;
        }

        .btn-primary:hover:not(:disabled) {
            background: #6B2342;
        }

        .btn-danger {
            background: #4B0000;
            color: #FFFFFF;
        }

        .btn-danger:hover:not(:disabled) {
            background: #cc0000;
        }

        .btn-cancel {
            background: #949494;
            color: #FFFFFF;
        }

        .btn-cancel:hover:not(:disabled) {
            background: #7E7B7B;
        }

        /* ==========================
           ACCIONES DE BÚSQUEDA
           ========================== */
        .search-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.8rem;
        }

        /* ==========================
           SECCIÓN DE INFORMACIÓN
           ========================== */
        .localidad-info-section {
            padding: 3rem 2rem;
            background: #FFFFFF;
            max-width: 900px;
            margin: 0 auto;
            display: none;
        }

        .localidad-info-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ==========================
           INFO BOX
           ========================== */
        .info-box {
            background: #D9D9D9;
            border: 2px solid #541C33;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0;
        }

        .info-box h3 {
            color: #541C33;
            margin-bottom: 1.8rem;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .info-item {
            padding: 0.8rem 1rem;
            background: #FFFFFF;
            border: 1px solid #949494;
        }

        .info-item strong {
            display: block;
            color: #4B0000;
            font-weight: 600;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
        }

        .info-item span {
            color: #000000;
            font-size: 0.9rem;
        }

        /* ==========================
           WARNING BOX
           ========================== */
        .warning-box {
            background: #4B0000;
            color: #FFFFFF;
            border: 2px solid #541C33;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .warning-box p {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .warning-box strong {
            font-weight: 700;
        }

        /* ==========================
           BOTONES DE ACCIÓN
           ========================== */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            padding-top: 1rem;
        }

        /* ==========================
           ESTADO DE CARGA
           ========================== */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 30px;
            height: 30px;
            margin: -15px 0 0 -15px;
            border: 3px solid #D9D9D9;
            border-top: 3px solid #541C33;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ==========================
           RESPONSIVO
           ========================== */
        @media (max-width: 768px) {
            .main-header h1 {
                font-size: 1.2rem;
            }

            .logo {
                width: 55px;
                height: 55px;
                font-size: 0.55rem;
            }

            .subheader {
                padding: 2rem 1rem;
            }

            .subheader h2 {
                font-size: 1.3rem;
            }

            .search-section {
                padding: 2rem 1rem;
            }

            .search-wrapper {
                padding: 2rem;
            }

            .search-actions {
                flex-direction: column;
            }

            .search-actions .btn {
                width: 100%;
            }

            .localidad-info-section {
                padding: 2rem 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <h1>Gestión de Localidades</h1>
        <div class="logo">CORREDOR<br>INTEROCEÁNICO</div>
    </header>

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="#" class="home-icon">🏠</a>
        <span class="separator">></span>
        <span>Eliminar localidades</span>
    </nav>

    <!-- Subheader -->
    <section class="subheader">
        <h2>Eliminar localidades</h2>
    </section>

    <div class="container">
        <!-- Sección de Búsqueda -->
        <section class="search-section">
            <div class="search-wrapper">
                <!-- Búsqueda por ID -->
                <div class="search-group">
                    <div class="form-group">
                        <label for="id_localidad">
                            Filtro de búsqueda: <span class="required">*</span>
                        </label>
                        <select id="id_localidad">
                            <option value="">Identificador de la Localidad</option>
                            <option value="1">Localidad 1</option>
                            <option value="2">Localidad 2</option>
                            <option value="3">Localidad 3</option>
                        </select>
                        <small class="helper-text">Búsqueda principal por ID</small>
                    </div>
                </div>

                <!-- Botones de Búsqueda -->
                <div class="search-actions">
                    <button type="button" class="btn btn-primary" id="btnBuscar">
                        Buscar
                    </button>
                </div>
            </div>
        </section>

        <!-- Información de la Localidad (Inicialmente oculta) -->
        <section id="localidadInfoSection" class="localidad-info-section">
            <div class="info-box">
                <h3>Información de la Localidad Seleccionada</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>ID:</strong> 
                        <span id="info_id">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Nombre del Centro de Trabajo:</strong> 
                        <span id="info_nombre_centro">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Ubicación Georeferenciada:</strong> 
                        <span id="info_ubicacion">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Población:</strong> 
                        <span id="info_poblacion">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Localidad:</strong> 
                        <span id="info_localidad">-</span>
                    </div>
                    <div class="info-item">
                        <strong>Estado:</strong> 
                        <span id="info_estado">-</span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <strong>Tipo de Instalación:</strong> 
                        <span id="info_tipo_instalacion">-</span>
                    </div>
                </div>
            </div>

            <!-- Advertencia -->
            <div class="warning-box">
                <p><strong>ADVERTENCIA:</strong> Esta acción es irreversible. La localidad será eliminada permanentemente del sistema.</p>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">
                <button type="button" class="btn btn-danger" id="btnEliminar" disabled>
                    Eliminar Localidad
                </button>
                <button type="button" class="btn btn-cancel" id="btnCancelar">
                    Cancelar
                </button>
            </div>
        </section>
    </div>

    <!-- JS -->
    <script>
        // Simulación de datos de localidades
        const localidadesData = {
            '1': {
                id: '001',
                nombre_centro: 'Centro Educativo Benito Juárez',
                ubicacion: '17.2546°N, 95.1892°W',
                poblacion: '1,250 habitantes',
                localidad: 'San Juan Colorado',
                estado: 'Oaxaca',
                tipo_instalacion: 'Escuela Primaria'
            },
            '2': {
                id: '002',
                nombre_centro: 'Centro de Salud Rural',
                ubicacion: '16.8734°N, 95.2341°W',
                poblacion: '850 habitantes',
                localidad: 'Santo Domingo',
                estado: 'Oaxaca',
                tipo_instalacion: 'Centro de Salud'
            },
            '3': {
                id: '003',
                nombre_centro: 'Biblioteca Comunitaria',
                ubicacion: '17.1123°N, 95.4567°W',
                poblacion: '2,100 habitantes',
                localidad: 'Santiago Jamiltepec',
                estado: 'Oaxaca',
                tipo_instalacion: 'Biblioteca Pública'
            }
        };

        // Referencias a elementos del DOM
        const selectLocalidad = document.getElementById('id_localidad');
        const btnBuscar = document.getElementById('btnBuscar');
        const btnEliminar = document.getElementById('btnEliminar');
        const btnCancelar = document.getElementById('btnCancelar');
        const localidadInfoSection = document.getElementById('localidadInfoSection');

        // Función para mostrar información de la localidad
        function mostrarInfoLocalidad(id) {
            const data = localidadesData[id];
            
            if (data) {
                document.getElementById('info_id').textContent = data.id;
                document.getElementById('info_nombre_centro').textContent = data.nombre_centro;
                document.getElementById('info_ubicacion').textContent = data.ubicacion;
                document.getElementById('info_poblacion').textContent = data.poblacion;
                document.getElementById('info_localidad').textContent = data.localidad;
                document.getElementById('info_estado').textContent = data.estado;
                document.getElementById('info_tipo_instalacion').textContent = data.tipo_instalacion;
                
                localidadInfoSection.classList.add('active');
                localidadInfoSection.style.display = 'block';
                btnEliminar.disabled = false;
            }
        }

        // Evento del botón Buscar
        btnBuscar.addEventListener('click', () => {
            const selectedId = selectLocalidad.value;
            
            if (!selectedId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selección requerida',
                    text: 'Por favor, selecciona una localidad para buscar.',
                    confirmButtonColor: '#541C33'
                });
                return;
            }

            // Simular búsqueda
            btnBuscar.classList.add('loading');
            
            setTimeout(() => {
                btnBuscar.classList.remove('loading');
                mostrarInfoLocalidad(selectedId);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Localidad encontrada',
                    text: 'La información de la localidad se ha cargado correctamente.',
                    confirmButtonColor: '#541C33',
                    timer: 2000
                });
            }, 800);
        });

        // Evento del botón Eliminar
        btnEliminar.addEventListener('click', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará permanentemente la localidad del sistema. ¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4B0000',
                cancelButtonColor: '#949494',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simular eliminación
                    btnEliminar.classList.add('loading');
                    
                    setTimeout(() => {
                        btnEliminar.classList.remove('loading');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'La localidad ha sido eliminada exitosamente.',
                            confirmButtonColor: '#541C33'
                        }).then(() => {
                            // Resetear formulario
                            selectLocalidad.value = '';
                            localidadInfoSection.style.display = 'none';
                            localidadInfoSection.classList.remove('active');
                            btnEliminar.disabled = true;
                        });
                    }, 1000);
                }
            });
        });

        // Evento del botón Cancelar
        btnCancelar.addEventListener('click', () => {
            Swal.fire({
                title: '¿Cancelar operación?',
                text: "Se descartará la selección actual.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#541C33',
                cancelButtonColor: '#949494',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    selectLocalidad.value = '';
                    localidadInfoSection.style.display = 'none';
                    localidadInfoSection.classList.remove('active');
                    btnEliminar.disabled = true;
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Operación cancelada',
                        text: 'La operación ha sido cancelada.',
                        confirmButtonColor: '#541C33',
                        timer: 2000
                    });
                }
            });
        });
    </script>
</body>
</html>