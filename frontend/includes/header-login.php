<!-- ============================================ -->
<!-- Archivo: header-login.php (archivo separado) -->
<!-- ============================================ -->

<?php
/*
<!DOCTYPE html> - NO incluir esto aquí
<html> - NO incluir esto aquí
<head> - NO incluir esto aquí
Solo el contenido del header y sus estilos
*/
?>
<style>
    .encabezado-principal {
        background-color: #F7F7F7;
        padding: 15px 30px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .contenedor-encabezado {
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .imagen-encabezado {
        max-width: 600px;
        width: 100%;
        height: auto;
        display: block;
    }

    @media (max-width: 768px) {
        .encabezado-principal {
            padding: 10px 15px;
        }

        .imagen-encabezado {
            max-width: 100%;
        }
    }
</style>

<header class="encabezado-principal">
    <div class="contenedor-encabezado">
        <img src="/assets/img/encabezado.jpeg" 
             alt="Encabezado MARINA-CIIT" 
             class="imagen-encabezado">
    </div>
</header>


