<link rel="stylesheet" href="/assets/css/headers-styles.css">   
<header class="barra-titulo">
    <div class="contenedor-titulo">
        <h1 class="texto-titulo">
            <?php echo isset($titulo_seccion) ? htmlspecialchars($titulo_seccion) : 'Titulo dinamico de módulo'; ?>
        </h1>
        <div class="logo-circular">
            <img src="/assets/img/logo_encabezado.jpeg"
                alt="Logo Corredor Interoceánico">
        </div>
    </div>
</header>