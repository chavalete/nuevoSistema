<?php 
if(empty($data['generales'])){
	$globalTools = false;
}else{
	foreach($data['generales'] as $k => $v){
		if($v == 1){
			$globalTools = true;
			break;
		}
	}
}
if(!empty($datosSeccion['flix_boton_top'])){ ?>
    <div id="flix_botonera">
        <?php 
        //var_dump($datosSeccion['flix_boton_top']);
        for($i=0; $i<count($datosSeccion['flix_boton_top']); $i++){ 
            $class = strtolower($datosSeccion['flix_boton_top'][$i]);
            $class = $seccion.'_'.str_replace(' ', '_', $class);
            //var_dump($class);//exit;
            //var_dump($_SESSION['botonera']);//exit;
            if($_SESSION['botonera'][strtolower($class)]){
            ?>
            <div class="flix_boton <?=$class?>" href="tpl/">
                <span class="texto_boton <?=$class?>" style="width: auto;"><?=$datosSeccion['flix_boton_top'][$i]?></span>
            </div>
            <div class="flix-separator top"></div>
            <?php 
            }
        }

	if($globalTools){?>
		<div id="globalTools" style="float: right;">
			<?php foreach($data['generales'] as $k => $v){ 
				if($v){ ?>	
					<div class="herramienta <?=$k;?> multiCheck"/>
					<?php 
				}
			} ?>
    </div>

		<?php
	}
        ?>
    </div>
<?php } ?>
