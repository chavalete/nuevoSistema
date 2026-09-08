<?php
include('../../inc/process.php');
$herramientas = false;
?>

<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Detalles</span>
    </div>
    
    <input id="error" type="hidden" value="0" />    
    
    <div class="detalles-cabecera-herramientas" style="float: right; margin-right: 7px;">
        <div class="detalle-cabecera-editar"></div>
        
        <div class="herramientasEdit-detalles" style="display: none;">
            <div class="editTool-detalles acept" title="Guargar"></div>
            <div class="editTool-detalles cancel" title="Cancelar"></div>
        </div>
    </div>
    
    
    <div class="detalles-cabecera">
        <table width="100%">
            <?php for($i=0; $i<count($data['propiedades']); $i++){ ?>
                <tr>
                    <td class="cabecera-title"><?=$data['propiedades'][$i]['display']?></td>
                    <td class="cabecera-detalle <?=(!empty($data['propiedades'][$i]['editable'])) ? 'editable' : '' ;?>">
                        <div class="cabecera-dato <?=(!empty($data['propiedades'][$i]['editable'])) ? 'editable' : '';?>"><?=$data['propiedades'][$i]['value']?></div>
                        <?php if(!empty($data['propiedades'][$i]['editable'])){ ?>
                            <div class="cabecera-input" style="display: none;">
                                <?php $params = (strstr($data['propiedades'][$i]['class'], 'autocomplete')) ? 'params="false"' : ''; ?>
                                <input class="required <?=$data['propiedades'][$i]['class'];?>" name="<?=$data['propiedades'][$i]['name']?>" value="<?=$data['propiedades'][$i]['value']?>" <?=$params;?> style="height: 17px; width: 50%;"/>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    
    <div class="detalles-table">
        <table width="100%">
            <thead>
                <tr>
                    <?php for($i=0; $i<count($data['listadoDetalles']['modelo']); $i++){ ?>
                        <?php if($i == 0){ ?>
                            <th class="detalles-titulo"><input type="checkbox" name="selectAll" style="width: 13px;"/></th>
                        <?php } ?>
                        <th class="detalles-titulo"><?=$data['listadoDetalles']['modelo'][$i]['display']?></td>
                        <?php $herramientas = ($data['listadoDetalles']['modelo'][$i]['name'] == 'herramienta' && !$herramientas) ? true : false; ?>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php for($i=0; $i<count($data['listadoDetalles']['celdas']); $i++){ 
                    $class = ($i%2) ? 'impar' : 'par';
                    if(!empty($data['listadoDetalles']['celdas'][$i]['bloqueado'])){
                        $class = 'bloqueado_'.$class;
                    }
                    ?>
                    <tr class="detalles-dato <?=$class?>" id="<?=$data['listadoDetalles']['celdas'][$i]['id'];?>">
                        <?php for($e=0; $e<count($data['listadoDetalles']['celdas'][$i]['cell']); $e++){ ?>
                            <?php if($e == 0){ ?>
                                <td class="detalles-titulo"  style="width: 13px; padding-left: 0; text-align: center;">
                                    <input type="checkbox" name="agrupar" style="width: 13px;"/>
                                </td>
                            <?php } ?>                        
                            <td>
                                <div class="detalle-dato <?=(!empty($data['listadoDetalles']['modelo'][$e]['editable']) && !empty($data['listadoDetalles']['celdas'][$i]['herramientas']['editable'])) ? 'editable' : '';?>"><?=$data['listadoDetalles']['celdas'][$i]['cell'][$e];?></div>

                                <?php if(!empty($data['listadoDetalles']['modelo'][$e]['editable']) && !empty($data['listadoDetalles']['celdas'][$i]['herramientas']['editable'])){ ?>
                                    <div class="detalle-input" style="display: none;">
                                        <input class="required <?=$data['listadoDetalles']['modelo'][$e]['class'];?>" name="<?=$data['listadoDetalles']['modelo'][$e]['name']?>" value="<?=$data['listadoDetalles']['celdas'][$i]['cell'][$e];?>" style="height: 17px; width: 90%;"/>
                                    </div>
                                <?php } ?>                        

                            </td>
                        <?php } ?>
                        <?php if($herramientas){ ?>
                            <td>
                                <div class="detalle-editar" title="Editar"></div>
                                <?php if($_POST['tipo'] == 'bloqueador'){ ?>
                                    <div class="herramienta-bloquear" title="Bloquear/Desbloquear">
                                        <img src="img/herramienta-bloquear.png" width="20" height="20" />
                                    </div>
                                <?php } ?>
                                
                                <div class="herramientasEdit-detalle" style="display: none;">
                                    <div class="editTool-detalle acept" title="Guargar"></div>
                                    <div class="editTool-detalle cancel" title="Cancelar"></div>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>    
    <hr>
    <div class="send-button" onclick="Javascript: $.colorbox.close();">Cerrar</div>
</div>

<script type="text/javascript">
    firstTime = true;
    window.error = false;
    window.cabecera = {
        accion: 'actualizarRegistro',
        parametros: {
            campos      : {},
            id          : '<?=$_POST['id'];?>',
            autogestion : '<?=$_POST['autogestion'];?>',
            tipo        : window.parametros.tipo,
            idsToGroup  : ''
        }
    };

    var mostrarEdicionCabecera = function(){
        $('div.cabecera-dato.editable', 'div.detalles-cabecera').hide();
        $('div.detalle-cabecera-editar', 'div.detalles-cabecera-herramientas').hide();
        $('div.cabecera-input', 'div.detalles-cabecera').show();
        $('div.herramientasEdit-detalles', 'div.detalles-cabecera-herramientas').show();        
    }
    var ocultarEdicionCabecera = function(){
        $('div.cabecera-input', 'div.detalles-cabecera').hide();
        $('div.herramientasEdit-detalles', 'div.detalles-cabecera-herramientas').hide();        
        $('div.cabecera-dato.editable', 'div.detalles-cabecera').show();
        $('div.detalle-cabecera-editar', 'div.detalles-cabecera-herramientas').show();        
    }

    // CABECERA
    $('div.detalle-cabecera-editar', 'div.detalles-cabecera-herramientas').click(function(){
        mostrarEdicionCabecera();
    });
    $('div.editTool-detalles.cancel', 'div.detalles-cabecera-herramientas').click(function(){
        ocultarEdicionCabecera();
    });
    $('div.editTool-detalles.acept', 'div.detalles-cabecera-herramientas').click(function(){
        var checked = false;
        window.cabecera.parametros.idsToGroup = '';
        
        $('input[name=agrupar]').each(function(){
            if(checked == false){
                checked = $(this).attr('checked') ? true : false;
            }
        });

        if(checked){
            $('input[name=agrupar]').each(function(){
                var addId = $(this).attr('checked') ? true : false;
                if(addId){
                    window.cabecera.parametros.idsToGroup += $(this).closest('tr').attr('id')+',';
                }
            });            
            
            var value;
            $('input#error', 'div#modal.inicio').val('0');
            $('input.required', $(this).closest('tr')).each(function(){
                window.validateInputs($(this));
            });     
            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('input:not(#error)', 'div.detalles-cabecera').each(function(){
                    if($(this).hasClass('autocomplete')){
                        value = $(this).attr('params');
                    }else{
                        value = $(this).val().trim();
                    }                
                    window.cabecera.parametros.campos[$(this).attr('name')] = value;
                });
	
                window.callService(window.cabecera.accion, window.cabecera.parametros);

                if(window.rsService.soyError == false){
                    ocultarEdicionCabecera();

                    $('input', 'div.detalles-cabecera').each(function(){
                        var td = $(this).closest('td');
                        $('div.cabecera-dato', td).html($(this).val());
                    }); 
                    $('td.cabecera-detalle.editable', 'div.detalles-cabecera').effect("highlight", {color: '#6EBA84'}, 3000);
                }else{
                    $.msgBox({ title: "Error", content: window.rsService.mensaje });
                }
            }
        }else{
            $.msgBox({ title: "Alerta", content: 'Debe seleccionar al menos un producto'});
        }
    });
    
    // LISTADO
    var ocultarEdits = function(){
        $('div.herramientasEdit-detalle', 'div.detalles-table').hide();
        $('div.detalle-input', 'div.detalles-table').hide();
        
        $('div.detalle-editar', 'div.detalles-table').show();
        $('div.detalle-dato.editable', 'div.detalles-table').show();
    }
    $('div.detalle-editar', 'tr.detalles-dato').click(function(){
        ocultarEdits();
        var trContent = $(this).closest('tr');
        /// QUILOMBO PARA AJUSTAR EL TAMAÑO DE LAS COLUMNAS
        if(firstTime){
            $('td', trContent).each(function(index){
                var td = $(this);
                var tdIndex = index;
                var tdWidth = Number(td.css('width').slice(0, -2)) - Number(td.css('padding-left').slice(0, -2));
                var inputWidth = tdWidth - 14;

                $('th.detalles-titulo').each(function(index){
                    if(index == tdIndex){
                        $(this).css('width', tdWidth+'px');
                        $('input', td).css('width', inputWidth+'px');
                    }
                });
            });    
            firstTime = false;
        }
        /////////////////////////////////////////////////////
        $('div.detalle-dato.editable', trContent).hide();
        $('div.detalle-editar', trContent).hide();
        $('div.detalle-input', trContent).show();
        $('div.herramientasEdit-detalle', trContent).show();
    });
    
    $('div.editTool-detalle.cancel').click(function(){
        var trContent = $(this).closest('tr');
        $('div.detalle-input', trContent).hide();
        $('div.herramientasEdit-detalle', trContent).hide();
        $('div.detalle-dato', trContent).show();
        $('div.detalle-editar', trContent).show();        
    });
    
    $('div.editTool-detalle.acept').click(function(){
        var value;
        $('input#error', 'div#modal.inicio').val('0');
        $('input.required', 'div.detalles-table').each(function(){
            window.validateInputs($(this));
        });     
        if($('input#error', 'div#modal.inicio').val() == '0'){
            var trContent = $(this).closest('tr');
            var params    = {
                id          : trContent.attr('id'),
                campos      : {},
                autogestion : '<?=$_POST['autogestion'];?>'
            };
            var service   = 'actualizarProducto';
            $('input', trContent).each(function(){
                if($(this).hasClass('autocomplete')){
                    value = $(this).attr('params');
                }else{
                    value = $(this).val().trim();
                }                      
                params.campos[$(this).attr('name')] = value;
            });
            
            window.callService(service, params);
            
            if(window.rsService.soyError == false){
                $('input', trContent).each(function(){
                    var td = $(this).closest('td');
                    $('div.detalle-dato', td).html($(this).val());
                });
                ocultarEdits();
                $('td', trContent).effect("highlight", {color: '#6EBA84'}, 3000);
            }else{
                $.msgBox({ title: "Error", content: window.rsService.mensaje });
            }
        }
    });
    $('div.herramienta-bloquear').click(function(){
        var tr = $(this).closest('tr');
        var block = (tr.hasClass('bloqueado_par') || tr.hasClass('bloqueado_impar')) ? true : false;
        var parametros = {
            bloqueado : block,
            id        : tr.attr('id')
        }
        window.callService('bloquearRegistro', parametros);
        
        if(block == true){
            if(tr.hasClass('bloqueado_par')){
                tr.removeClass('bloqueado_par');
                tr.addClass('par')
            }else if(tr.hasClass('bloqueado_impar')){
                tr.removeClass('bloqueado_impar');
                tr.addClass('impar')
            }
        }else{
            if(tr.hasClass('par')){
                tr.removeClass('par');
                tr.addClass('bloqueado_par')
            }else if(tr.hasClass('impar')){
                tr.removeClass('impar');
                tr.addClass('bloqueado_impar')
            }
        }
    });
    $('input[name=selectAll]').click(function(){
        var checked = $(this).attr('checked') ? true : false;
        if(checked){
            $('input[name=agrupar]').attr('checked', 'checked');
        }else{
            $('input[name=agrupar]').removeAttr('checked');    
        }
    });
</script>
