<?php
$params      = $_POST['parametros'];
$perPage     = !empty($params['porPagina']) ? $params['porPagina'] : ParseINI::getConfig('defaults_js', 'porPagina');
$desde       = ($params['pagina'] == 1) ? 1 : ($perPage * ($params['pagina'] - 1) + 1); 
$hasta       = ($params['pagina'] == 1) ? $perPage : $perPage * ($params['pagina']);
$total_pages = ceil($data['colData']['total'] / $perPage);
$total_data  = $data['colData']['total'];
$page        = $data['colData']['page'];
?>

<div id="flix-bottom">
    <div id="show-per-page">
        <select class="perPage">
            <option <?=($perPage == 10) ? 'selected' : ''; ?> value="10">10</option>
            <option <?=($perPage == 15) ? 'selected' : ''; ?> value="15">15</option>
            <option <?=($perPage == 20) ? 'selected' : ''; ?> value="20">20</option>
            <option <?=($perPage == 30) ? 'selected' : ''; ?> value="30">30</option>
            <option <?=($perPage == 50) ? 'selected' : ''; ?> value="50">50</option>
        </select>                    
    </div>

    <div class="flix-separator bottom"></div>

    <div id="flix-paginador">
        <div class="bottom-button first <?=($page == 1) ? 'opacity' : '' ;?>" title="Ir a la primer pagina"></div>
        <div class="bottom-button prev <?=($page == 1) ? 'opacity' : '' ;?>" title="Pagina anterior"></div>
        <div class="flix-separator bottom"></div>
        <div style="float: left;">
            Pagina 
            <input id="input_perPage" type="text" size="3" value="<?=$page;?>"/>
            de 
            <span class="total_pages"><?=$total_pages;?></span>
        </div>
        <div class="flix-separator bottom"></div>
        <div class="bottom-button next <?=($total_pages == $page) ? 'opacity' : '' ;?>" title="Proxima pagina"></div>
        <div class="bottom-button last <?=($total_pages == $page) ? 'opacity' : '' ;?>" title="Ir a la ultima pagina"></div>
    </div>

    <div class="flix-separator bottom"></div>
    <div id="flix-actualizar" class="bottom-button" title="Actualizar"></div>
    <div class="flix-separator bottom"></div>

    <div id="flix-grid-info">
        Mostrando <?=$desde;?> a <?=$hasta;?> de <?=$total_data;?> items
    </div>

</div>