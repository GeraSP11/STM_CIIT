<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Marina - Corredor Interoceánico'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .header-container {
            background-color: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo-marina {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-marina img {
            height: 70px;
            width: auto;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header-container">
        <div class="header-content">
            <div class="logo-marina">
                <!-- Escudo de México -->
                <img src="<?php echo isset($base_url) ? $base_url : ''; ?>../../../../assets/img/encabezado.jpeg" alt="Encabezado MARINA-CIIT">
            </div>
        </div>
    </header>