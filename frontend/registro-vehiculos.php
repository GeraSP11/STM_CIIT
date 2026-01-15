<?php
require_once "../backend/middleware/role.php";
require_once "../backend/middleware/no-cache.php";
requireRole(["Autoridad", "Administrador del TMS"]);

$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Vehículos';
$seccion = 'Armar Vehículo';

// Simulación de variables para la estructura que pediste
$active_tab = 'tab_ensamble'; 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/headers-styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4a1026;
            --secondary-color: #6c757d;
            --error-color: #dc3545;
        }

        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            width: 95%;
            max-width: 1300px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Estructura de Grid solicitada */
        .ensamble-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .form-column, .table-column {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input[type="text"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
        }

        .btn-submit-ensamble {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.3s;
        }

        .btn-submit-ensamble:hover {
            background-color: #3b0d20;
        }

        /* Tabla de carrocerías */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .hidden { display: none; }
        h2 { text-align: center; color: var(--primary-color); }
    </style>
</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <div class="form-container">
        <div id="tab_ensamble" class="tab-content <?php echo ($active_tab !== 'tab_ensamble') ? 'hidden' : ''; ?>">
            <h2><?php echo $seccion; ?></h2>
            
            <form id="formVehiculos">
                <div class="ensamble-grid">
                    
                    <div class="form-column">
                        <h4 style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 5px;">
                            <i class=""></i> Datos del Vehículo Principal
                        </h4>

                        <label for="modalidad_vehiculo">Modalidad:</label>
                        <select id="modalidad_vehiculo" name="modalidad_vehiculo" required>
                            <option value="">-- Seleccionar Modalidad --</option>
                            <option value="Carretero">Carretero (Mín. 2)</option>
                            <option value="Ferroviario">Ferroviario (Mín. 2)</option>
                            <option value="Marítimo">Marítimo (Exacto 1)</option>
                            <option value="Aéreo">Aéreo (Exacto 1)</option>
                        </select>

                        <label for="descripcion_vehiculo">Descripción / Modelo:</label>
                        <input type="text" id="descripcion_vehiculo" name="descripcion_vehiculo" required 
                               placeholder="Ej: Tractocamión Kenworth, Buque de Carga">

                        <label for="chofer_asignado">Chofer Asignado (Operador Logístico):</label>
                        <select id="chofer_asignado" name="chofer_asignado" required>
                            <option value="">-- Seleccionar Chofer --</option>
                        </select>

                        <button type="submit" class="btn-submit-ensamble">
                            <i class=""></i> Registrar Vehículo y Ensamblar Carrocerías
                        </button>
                    </div>

                    <div class="table-column">
                        <h4 style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 5px;">
                            <i class=""></i> Selección de Carrocerías Disponibles
                        </h4>
                        
                        <p style="color: var(--secondary-color); font-size: 0.9em; margin-top: 10px;">
                            <i class="fas fa-info-circle"></i> Marca las carrocerías a acoplar al nuevo vehículo.
                        </p>

                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 6px; background: #fafafa;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Sel.</th>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>N° Serie</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_carrocerias">
                                    <tr><td colspan="4" class="text-center">Cargando activos...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            cargarChoferes();
            cargarCarrocerias();

            // Lógica de Envío y Validación RF-GV-02
            document.getElementById('formVehiculos').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const modalidad = document.getElementById('modalidad_vehiculo').value;
                const seleccionadas = document.querySelectorAll('input[name="ids_carrocerias[]"]:checked');
                const cantidad = seleccionadas.length;

                // Reglas de negocio del PDF
                if ((modalidad === 'Marítimo' || modalidad === 'Aéreo') && cantidad !== 1) {
                    Swal.fire('Error de Validación', 'La modalidad ' + modalidad + ' requiere exactamente 1 carrocería.', 'error');
                    return;
                }

                if ((modalidad === 'Carretero' || modalidad === 'Ferroviario') && cantidad < 2) {
                    Swal.fire('Error de Validación', 'La modalidad ' + modalidad + ' requiere mínimo 2 carrocerías.', 'error');
                    return;
                }

                if (cantidad === 0) {
                    Swal.fire('Atención', 'Debe seleccionar al menos una carrocería.', 'warning');
                    return;
                }

                const formData = new FormData(this);
                formData.append('action', 'armar-vehiculo');

                fetch('/ajax/vehiculos-ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.exito) {
                        Swal.fire('¡Éxito!', 'Vehículo registrado y ensamblado correctamente.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.mensaje || 'Error en el proceso.', 'error');
                    }
                });
            });
        });

        function cargarChoferes() {
            fetch('/ajax/personal-ajax.php', {
                method: 'POST',
                body: new URLSearchParams({'action': 'consultar-operadores'})
            })
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('chofer_asignado');
                select.innerHTML = '<option value="">-- Seleccionar Chofer --</option>';
                data.forEach(p => {
                    let opt = document.createElement('option');
                    opt.value = p.id_personal;
                    opt.textContent = `${p.id_personal} - ${p.nombre_personal} ${p.apellido_paterno}`;
                    select.appendChild(opt);
                });
            });
        }

        function cargarCarrocerias() {
            const tbody = document.getElementById('lista_carrocerias');
            fetch('/ajax/carroceria-ajax.php', {
                method: 'POST',
                body: new URLSearchParams({'action': 'consultar-carrocerias'})
            })
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay carrocerías disponibles.</td></tr>';
                    return;
                }
                data.forEach(c => {
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="checkbox" name="ids_carrocerias[]" value="${c.id_carroceria}"></td>
                        <td>${c.id_carroceria}</td>
                        <td>${c.tipo_carroceria}</td>
                        <td>${c.numero_serie}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }
    </script>
</body>
</html>