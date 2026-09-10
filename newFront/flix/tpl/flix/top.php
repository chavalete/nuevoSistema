<?php
$helimonMorenoTool = isset($_POST['parametros']['tipo']) ? $_POST['parametros']['tipo'] : $data['title'];
$helimonMorenoTool .= '_landing';
?>

<div id="flix_titulo">
    <?=$data['title'];?>
</div>

<!-- Tooltip -->
<div class='tooltip'>
    <div class="tooltip-position" style='top: -30px; right: 5px; position: relative;'>
        <img class="tooltip-click" src="img/ayuda.png" />
        <div class="tooltip-content" style="left: -430px; top: -20px; "></div>
    </div>
</div>
<!-- Fin de la Tooltip -->