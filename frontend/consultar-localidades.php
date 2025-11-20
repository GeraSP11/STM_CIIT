<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Productos</title>
    <style>
        /* ============================
           GENERAL
        ============================ */
        body {
            font-family: "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        /* ============================
           HEADER
        ============================ */
        .header {
            background: #5D2E46;
            color: #ffffff;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 300;
            margin: 0;
            flex: 1;
        }

        .header-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ============================
           BREADCRUMB
        ============================ */
        .breadcrumb {
            background: white;
            padding: 15px 40px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .breadcrumb-icon {
            font-size: 24px;
            color: #5D2E46;
        }

        .breadcrumb span {
            color: #333;
        }

        /* ============================
           CONTENIDO PRINCIPAL
        ============================ */
        .main-content {
            padding: 40px 80px;
            background: white;
        }

        .page-title {
            text-align: center;
            color: #6B4423;
            font-size: 2rem;
            font-weight: 400;
            margin-bottom: 40px;
            margin-top: 0;
        }

        /* ============================
           SECCIÓN DE BÚSQUEDA
        ============================ */
        .search-section {
            max-width: 400px;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-weight: 400;
            color: #333;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        input[type="text"],
        select {
            width: 300px;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #999;
            background-color: #fff;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s ease;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: #5D2E46;
            outline: none;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            padding-right: 40px;
            color: #999;
        }

        /* ============================
           BOTÓN CONSULTAR
        ============================ */
        .btn-wrapper {
            text-align: center;
            margin-top: 80px;
        }

        .btn-consultar {
            padding: 12px 40px;
            background-color: #5D2E46;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-consultar:hover {
            background-color: #4A2338;
        }

        /* ============================
           RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .header-logo {
                width: 50px;
                height: 50px;
            }

            .breadcrumb {
                padding: 12px 20px;
            }

            .main-content {
                padding: 30px 20px;
            }

            .page-title {
                font-size: 1.5rem;
                margin-bottom: 30px;
            }

            input[type="text"],
            select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>Consulta de Productos</h1>
        <div class="header-logo">
            <img src="logo.png" alt="Corredor Interoceánico" onerror="this.style.display='none'">
        </div>
    </header>

    <div class="breadcrumb">
        <span class="breadcrumb-icon">🏠</span>
        <span>&gt;</span>
        <span><strong>Consultar Productos</strong></span>
    </div>

    <main class="main-content">
        <h2 class="page-title">Consultar Productos</h2>

        <section class="search-section">
            <h3 class="section-title">Búsqueda por:</h3>

            <form action="/backend/consultaProductos.php" method="POST">
                <div class="form-group">
                    <label for="nombreProducto">Nombre del producto :</label>
                    <input type="text" id="nombreProducto" name="nombreProducto">
                </div>

                <div class="form-group">
                    <label for="ubicacion">Aplicar un filtro:</label>
                    <select id="ubicacion" name="ubicacion">
                        <option value="" selected>Ubicación del producto</option>
                        <option value="almacen1">Almacén 1</option>
                        <option value="almacen2">Almacén 2</option>
                        <option value="bodega">Bodega Principal</option>
                    </select>
                </div>

                <div class="btn-wrapper">
                    <button type="submit" class="btn-consultar">Consultar</button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>