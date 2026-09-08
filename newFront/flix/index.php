<?php include('inc/master_header.php');

if($_SESSION['sistema'] !="limonMoreno"){

    session_destroy();
    header('Location: login.php');
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <!-- Incluyo los css y js de la seccion -->
        <?=Includes::get('common', 'css')?>
        <?=Includes::get('common', 'js')?>
        <script type="text/javascript">
            var accion = '<?=ParseINI::getConfig('defaults_js', 'accion')?>';
            var parametros = {
                ordenarOrden : '',
                ordenarPor   : '',
                pagina       : '<?=ParseINI::getConfig('defaults_js', 'pagina')?>',
                porPagina    : '<?=ParseINI::getConfig('defaults_js', 'porPagina')?>',
                                   tipo         : '<?=$_SESSION['paginaInicio'] ?>'
            };
            var accion_aux = accion;
            window.urlServices = 'http://<?=$_SERVER['HTTP_HOST']?>/limonMoreno/';

            window.allSeccionData = <?=ParseINI::getAllJson();?>;
        </script>
    </head>

    <body>

        <div id="main">
            <?php include('tpl/header.php'); ?>
            <?php include('tpl/body.php'); ?>
            <?php include('tpl/footer.php'); ?>
        </div>
    </body>
</html>
