<?php
// El Panel Principal (Tablero) no usa esta grilla genérica: tiene su propia
// pantalla en panelPrincipal.php. Si el autoload inicial de flix.js llega
// hasta acá con este tipo (por ejemplo, al volver a esa sección desde otra
// parte del menú), no renderizamos ninguna grilla.
//
// OJO: igual hay que devolver el bloque "parametros_aux" de más abajo.
// Ese script solo se define cuando llega la PRIMERA respuesta de este
// archivo (jQuery ejecuta el <script> al insertarlo en el DOM); si acá
// cortamos con exit y no llega nunca, flixResetValues() deja
// window.parametros en undefined para el resto de la sesión y el menú
// de secciones se rompe apenas lo tocás (aunque sea en otra pantalla).
if(!empty($_POST['parametros']['tipo']) && $_POST['parametros']['tipo'] === 'principal'){
    include('../../inc/master_header.php');
    ?>
    <script>
        var parametros_aux = {
            ordenarOrden : '',
            ordenarPor   : '',
            pagina       : '<?=ParseINI::getConfig('defaults_js', 'pagina')?>',
            porPagina    : '<?=ParseINI::getConfig('defaults_js', 'porPagina')?>',
            tipo         : '<?=ParseINI::getConfig('defaults_js', 'tipo')?>'
        };
    </script>
    <?php
    exit;
}

include('../../inc/master_header.php');
include('../../inc/process.php');

// Levanto los datos del config.ini
$datosSeccion = ParseINI::getConfig($seccion);

// TPL de titulo de la seccion
include('top.php');
// TPL de la botonera de la seccion
include('botonera.php'); 
$herramientas = false;

/*
echo '<pre>';
print_r($data);
echo '</pre>';
*/
?>
<script>            
    var parametros_aux = {
        ordenarOrden : '',
        ordenarPor   : '',
        pagina       : '<?=ParseINI::getConfig('defaults_js', 'pagina')?>',
        porPagina    : '<?=ParseINI::getConfig('defaults_js', 'porPagina')?>',
        tipo         : '<?=ParseINI::getConfig('defaults_js', 'tipo')?>'
    };
</script>    

<!-- Contenedor de la grilla que se carga mediante JS: Flix. -->
<table id="grid-content" bordercolor="#D6D6D6" rules="cols">
    <?php if(empty($data) || count($data['colData']['rows']) == 0){ ?>
        <tr class="grid-datos" style="color: red; font-weight: bold; height: 100px;"><td>No hay datos</td></tr>
    <?php } ?>    
    
    <tr>
        <?php for($i=0; $i<count($data['colModel']); $i++){ ?>
            <th class="grid-titulo" params="<?=$data['colModel'][$i]['name'];?>">
                
                <div style="width: 98%;"><?=$data['colModel'][$i]['display']?></div>
                
                <div class="fieldSorter">
                    <?php if(!empty($data['colModel'][$i]['sortable'])){ ?>
                        <div class="fieldSorter-asc" title="Orden ascendente">
                            <?php 
                            if($data['colModel'][$i]['name'] == $data['colOrder']['ordenarPor'] && $data['colOrder']['ordenarOrden'] == 'asc'){ 
                                $class = 'asc-hover';
                                $sign   = $i;
                            }else{
                                $class = 'asc';
                            }
                            ?>
                            <div class="<?=$class;?>"></div>
                        </div>
                        <div class="fieldSorter-separator"></div>
                        <div class="fieldSorter-asc" title="Orden descendente">
                            <?php 
                            if($data['colModel'][$i]['name'] == $data['colOrder']['ordenarPor'] && $data['colOrder']['ordenarOrden'] == 'desc'){ 
                                $class = 'desc-hover';
                                $sign   = $i;
                            }else{
                                $class = 'desc';
                            }
                            ?>                            
                            
                            
                            <div class="<?=$class;?>"></div>
                        </div>
                    <?php } ?>
                </div>
                
            </th>
            <?php
            if($herramientas == false && $data['colModel'][$i]['display'] == 'Herramientas'){
                $herramientas = true;
            }
        } ?>
    </tr>

    <?php for($i=0; $i<count($data['colData']['rows']); $i++){?>
            
        <tr class="grid-datos <?=(($i+1)%2 == 0) ? 'par' : '';?>" id="<?=$data['colData']['rows'][$i]['id'];?>">
            <?php for($e=0; $e<count($data['colData']['rows'][$i]['cell']); $e++){ 
                $tdClass = '';
                foreach($data['colData']['rows'][$i]['herramientas'] as $key => $value){
                    if($value){ 
                        $tdClass .= $key.' ';
                    }
                }
                if($e == $sign){
                    $tdClass .= ' sorted';
                    if((($i+1)%2 == 0)){
                        $tdClass .= '-par';
                    }else{
                        $tdClass .= '-impar';
                    }
                }
                if(strstr($data['colModel'][$e]['class'], 'select')){ 
                    $tdClass .= ' select ';
                }
                ?>
            
                <td class="<?=$tdClass?>">
                    <?php 
                    if(!empty($data['colData']['rows'][$i]['herramientas']['editable']) && !empty($data['colModel'][$e]['editable'])){ 
                        $inputClass = 'editData ';
                        $inputClass .= $data['colModel'][$e]['class'].' ';
                        $inputClass .= !empty($data['colModel'][$e]['required']) ? 'required ' : '';
                        ?>
                        <?php $params = (strstr($data['colModel'][$e]['class'], 'autocomplete')) ? 'params="false"' : ''; ?>
                        <div class="showData">
                            <?php 
                            if(is_array($data['colData']['rows'][$i]['cell'][$e])){ 
                                echo $data['colData']['rows'][$i]['cell'][$e]['label'];
                            }else{
                                echo $data['colData']['rows'][$i]['cell'][$e];
                            } 
                            ?>
                        </div>
                        
                        <?php if(strstr($data['colModel'][$e]['class'], 'select')){ ?>
                            <select name="<?=$data['colModel'][$e]['name']?>" class="<?=$inputClass;?>" value="<?=$data['colData']['rows'][$i]['cell'][$e]['value'];?>">
                                <?php for($k=0; $k<count($data['colData']['rows'][$i]['cell'][$e]['select']); $k++){ ?>
                                    <?php $selected = $data['colData']['rows'][$i]['cell'][$e]['value'] == $data['colData']['rows'][$i]['cell'][$e]['select'][$k]['value'] ? 'selected="selected"' : ''; ?>
                                    <option value="<?=$data['colData']['rows'][$i]['cell'][$e]['select'][$k]['value'];?>" <?=$selected;?>>
                                        <?=$data['colData']['rows'][$i]['cell'][$e]['select'][$k]['label'];?>
                                    </option>
                                <?php } ?>
                            </select>
                        <?php }else{ ?>
                            <input name="<?=$data['colModel'][$e]['name']?>" class="<?=$inputClass;?>" type="input" value="<?=$data['colData']['rows'][$i]['cell'][$e];?>" <?=$params?>/>
                        <?php } ?>
                            
                    <?php 
                    }else{ 
                        if(is_array($data['colData']['rows'][$i]['cell'][$e])){ 
                            echo $data['colData']['rows'][$i]['cell'][$e]['label'];
                        }else{
                            echo $data['colData']['rows'][$i]['cell'][$e];
                        } 
                    } 
                    ?>
                </td>
            <?php } ?>
            
            <?php if($herramientas){ ?>
                <td class="herramientas">
                    <div class="herramientasGeneral">
                        <?php foreach($data['colData']['rows'][$i]['herramientas'] as $key => $value){ ?>
                            <?php if($value){ ?>
                                <div class="herramienta <?=$key;?>"></div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if(!empty($data['colData']['rows'][$i]['herramientas']['editable'])){ ?>
                        <div class="herramientasEdit">
                            <div class="editTool acept" title="Guargar"></div>
                            <div class="editTool cancel" title="Cancelar"></div>
                            <div class="editTool-loading" title="Cargando">
                                <img src="img/loading-little.gif" width="30" height="28" />
                            </div>
                        </div>
                    <?php } ?>
                </td>  
            <?php } ?>
        </tr>
    <?php } ?>    
</table>

<?php 
if(!empty($data) && count($data['colData']['rows']) > 0){ 
    include('bottom.php'); 
}
?>
