<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Actualizar Localidades';
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

    <style>
        /* Estilo del datalist */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
        }

        /* Personalización del datalist */
        datalist {
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        option {
            padding: 8px;
            cursor: pointer;
        }

        option:hover {
            background-color: #f0f0f0;
        }

        /* Ocultar el contenedor de búsqueda después de seleccionar */
        #contenedorBusqueda {
            display: block;
        }

        #contenedorBusqueda.oculto {
            display: none;
        }

        /* Estilo para los botones después de seleccionar localidad */
        #contenedorBotones {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <?php include('includes/header-dinamico.php'); ?>

    <!-- Breadcrumb inline -->
    <nav aria-label="breadcrumb" class="mt-2" style="padding-left: 15px; font-size: 18px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color: #4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <!-- Título principal -->
    <h2 style="text-align:center; color:#4a1026; margin-top:10px; margin-bottom:30px;">
        <?php echo $seccion; ?>
    </h2>

    <!-- Barra superior de búsqueda -->
    <div id="contenedorBusqueda"
        style="width:80%; margin:20px auto; display:flex; align-items:center; gap:10px; position:relative;">
        <div style="background:#4a1026; color:white; padding:8px 15px; font-weight:bold; border-radius:3px;">
            Nombre de la Localidad:
        </div>
        <div style="flex:1; position:relative;">
            <input type="text" id="inputBuscarLocalidad" placeholder="Escribe el nombre de la Localidad"
                autocomplete="off" list="localidades"
                style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
            <datalist id="localidades">
                <!-- Las opciones serán agregadas dinámicamente -->
            </datalist>
        </div>
    </div>

    <!-- Contenedor principal -->
    <form id="formActualizarLocalidad" method="POST">
        <div
            style="width:80%; margin:25px auto; background:#f8f9fa; padding:35px; border-radius:8px; border:1px solid #ccc;">

            <!-- ID Localidad (oculto para envío) -->
            <input type="hidden" name="id_localidad" id="inputIdLocalidad">

            <!-- ID Localidad visible (solo lectura) -->
            <div style="margin-bottom:25px; text-align:center;">
                <label style="font-weight:bold; display:block; margin-bottom:6px;">ID Localidad:</label>
                <input type="text" id="inputIdLocalidadDisplay" readonly placeholder="ID"
                    style="padding:8px; width:200px; border:1px solid #bbb; border-radius:4px; display:inline-block; background:#e9ecef;">
            </div>

            <!-- Fila 1 -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">
                        Nombre del Centro de Trabajo *
                    </label>
                    <input type="text" name="nombre_centro_trabajo" id="inputNombreCentro"
                        placeholder="Nombre del Centro de Trabajo" required maxlength="100" min="10"
                        pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ0-9\s\-]+$"
                        style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">
                        Ubicación Georreferenciada (Latitud, Longitud) *
                    </label>
                    <input type="text" name="ubicacion_georeferenciada" id="inputUbicacion"
                        placeholder="Ej: 16.8531,-96.7712" required pattern="^-?\d{1,3}\.\d+,\s*-?\d{1,3}\.\d+$"
                        title="Formato: latitud,longitud (Ej: 16.8531,-96.7712)"
                        style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Población *</label>
                    <input type="text" name="poblacion" id="inputPoblacion" placeholder="Nombre de la población"
                        required minlength="2" maxlength="100" pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$"
                        title="Solo letras" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                </div>
            </div>

            <!-- Fila 2 -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Estado *</label>
                    <select name="estado" id="estados" required
                        style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                        <option value="">Seleccione un estado</option>
                        <!-- Opciones cargadas dinámicamente -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Tipo de Instalación *</label>
                    <select name="tipo_instalacion" id="selectTipoInstalacion" required
                        style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                        <option value="">Seleccione un tipo de instalación</option>
                        <option value="Centro Productivo">Centro Productivo</option>
                        <option value="Centro de Distribucion">Centro de Distribucion</option>
                        <option value="PODEBI">PODEBI</option>
                        <option value="Almacen">Almacen</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label style="font-weight:bold; display:block; margin-bottom:6px;">Localidad *</label>
                    <input type="text" name="localidad" id="inputLocalidad" placeholder="Nombre de la localidad"
                        required minlength="2" maxlength="100" pattern="^(?=.*[A-Za-zÀ-ÿÑñ])[A-Za-zÀ-ÿÑñ\s\-]+$"
                        title="Solo letras" style="width:100%; padding:10px; border:1px solid #bbb; border-radius:4px;">
                </div>
            </div>

            <!-- Nota -->
            <p style="font-size:14px; color:#444;">*Campos obligatorios</p>

            <!-- Botones -->
            <div id="contenedorBotones" style="text-align:center; margin-top:20px; display:none;">
                <button type="submit"
                    style="background:#4a1026; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer; margin-right:15px;">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" onclick="window.location.href='dashboard.php'"
                    style="background:#A00032; color:white; padding:12px 35px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>

        </div>
    </form>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- SWEETALERT2 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/swal/sweetalert2.min.css">
    <script src="/assets/libs/swal/sweetalert2.min.js"></script>

    <!-- ARCHIVO QUE CONTIENE alerta() y confirmar() -->
    <script src="/assets/js/alertas.js"></script>

    <!-- CRIPT DEL FORMULARIO -->

    <script src="/assets/js/estados.js"></script>
    <script src="/assets/js/localidades.js"></script>


</body>

</html>