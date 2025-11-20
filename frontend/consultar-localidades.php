<?php
$page_title = 'MARINA Corredor Interoceánico';
$titulo_seccion = 'Gestión de Localidades';
$seccion = 'Consultar Localidades';
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
        .content-area {
            background: #f5f5f5;
            padding: 40px;
            margin: 0 auto;
            width: 70%;
            min-height: 450px;
            border-radius: 5px;
        }

        .footer-line {
            margin-top: 80px;
            height: 8px;
            background: #4a1026;
            width: 100%;
        }

        .btn-add {
            padding: 10px 18px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 18px;
        }

        .btn-add:hover {
            background: #eaeaea;
        }

        .section-title {
            text-align: center;
            font-size: 24px;
            color: #4a1026;
            margin-bottom: 30px;
        }
    </style>

</head>

<body>

    <?php include('includes/header-dinamico.php'); ?>

    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb" style="padding-left: 15px;">
            <li class="breadcrumb-item">
                <a href="/dashboard.php"><i class="fas fa-home" style="color:#4D2132;"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo $seccion; ?>
            </li>
        </ol>
    </nav>

    <div class="content-area shadow">

        <div class="section-title">
            <?php echo $seccion; ?>
        </div>

        <div class="text-center">

            <select class="form-select d-inline-block" style="width: 55%;">
                <option>Selecciona un filtro</option>
            </select>

            <button class="btn-add">
                <i class="fas fa-plus"></i>
            </button>

        </div>

    </div>

    <div class="footer-line"></div>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
