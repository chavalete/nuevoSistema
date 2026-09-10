<?php
include('../../inc/process.php');
$herramientas = false;


if(!empty($_POST['autogestion'])){
    $helimonMorenoParams = 'detalles';
}else{
    $helimonMorenoParams = 'detalles_autogestion';
}

?>

<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Detalles</span>

        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="<?=$helimonMorenoParams;?>" style="top: 30px; left: -300px; width: 320px; height: 325px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->
    </div>

    <input id="error" type="hidden" value="0" />

    <div class="detalles-cabecera-herramientas" style="float: right; margin-right: 7px;">
        <?php if(!empty($data['alertar'])){ ?>
            <div class="herramienta-alertar"></div>
        <?php } ?>
        <?php if(!empty($data['imprimir'])){ ?>
            <div class="herramienta-imprimirDetalle"></div>
        <?php } ?>
        <?php if(!empty($data['editable_cabecera'])){ ?>
            <div class="detalle-cabecera-editar"></div>

            <div class="herramientasEdit-detalles" style="display: none;">
                <div class="editTool-detalles acept" title="Guargar"></div>
                <div class="editTool-detalles cancel" title="Cancelar"></div>
            </div>
        <?php } ?>
    </div>
    <div style="clear: both; margin: 5px 0;"></div>
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
                    <?php if(!empty($data['alertar'])){ ?>
                        <th class="detalles-titulo"><input type="checkbox" name="selectAll" style="width: 13px;"/></th>
                    <?php } ?>
                    <?php for($i=0; $i<count($data['listadoDetalles']['modelo']); $i++){ ?>
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
                    }else if(!empty($data['listadoDetalles']['celdas'][$i]['alertado'])){
                        $class = 'alertado_'.$class;
                    }
                    ?>
                    <tr class="detalles-dato <?=$class?>" id="<?=$data['listadoDetalles']['celdas'][$i]['id'];?>">
                        <?php if(!empty($data['alertar'])){ ?>
                            <td class="detalles-titulo" style="width: 13px; padding-left: 0; text-align: center;">
                                <?php if(empty($data['listadoDetalles']['celdas'][$i]['alertado'])){ ?>
                                    <input type="checkbox" name="alertar" style="width: 13px;"/>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <?php for($e=0; $e<count($data['listadoDetalles']['celdas'][$i]['cell']); $e++){ ?>
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
                                <?php if(!empty($data['listadoDetalles']['celdas'][$i]['herramientas']['editable'])){ ?>
                                    <div class="detalle-editar" title="Editar"></div>
                                    <div class="herramientasEdit-detalle" style="display: none;">
                                        <div class="editTool-detalle acept" title="Guargar"></div>
                                        <div class="editTool-detalle cancel" title="Cancelar"></div>
                                    </div>
                                <?php } ?>
                                <?php if($_POST['tipo'] == 'bloqueador'){ ?>
                                    <div class="herramienta-bloquear" title="Bloquear/Desbloquear">
                                        <img src="img/herramienta-bloquear.png" width="20" height="20" />
                                    </div>
                                <?php } ?>
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
            id          : <?=$_POST['id'];?>,
            autogestion : '<?=$_POST['autogestion'];?>',
            tipo        : window.parametros.tipo
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
                $.msgBox({ title: "Error", content: window.rsService.mensaje, type: 'error' });
            }
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

    $('div.editTool-detalle.acept').click(function(e){
        e.preventDefault(); // Previene interferencias de comportamiento de plugins terceros
        var value;
        $('input#error', 'div#modal.inicio').val('0');

        var trContent = $(this).closest('tr');

        // CORRECCIÓN: Validamos de manera específica los elementos obligatorios de este registro
        $('input.required', trContent).each(function(){
            window.validateInputs($(this));
        });

        if($('input#error', 'div#modal.inicio').val() == '0'){
            var params    = {
                id          : trContent.attr('id'),
                tipo        : window.parametros.tipo,
                campos      : {},
                autogestion : '<?=$_POST['autogestion'];?>'
            };
            var service   = 'actualizarProducto';

            $('input', trContent).each(function(){
                var inputName = $(this).attr('name');

                // Evita problemas si el plugin clonó el nodo sin asignarle una propiedad name
                if(!inputName) return true;

                if($(this).hasClass('autocomplete')){
                    var attrParams = $(this).attr('params');
                    // MÉTODO DE RESCATE: Si params no existe o es un valor 'false' por defecto,
                    // toma el valor textual del campo para evitar strings nulos o vacíos indeseados.
                    value = (attrParams && attrParams !== 'false') ? attrParams : $(this).val().trim();
                }else{
                    value = $(this).val().trim();
                }

                params.campos[inputName] = value;
            });

            window.callService(service, params);

            if(window.rsService.soyError == false){
                $('input', trContent).each(function(){
                    if($(this).attr('name')) {
                        var td = $(this).closest('td');
                        $('div.detalle-dato', td).html($(this).val());
                    }
                });
                ocultarEdits();
                $('td', trContent).effect("highlight", {color: '#6EBA84'}, 3000);
            }else{
                $.msgBox({ title: "Error", content: window.rsService.mensaje, type: 'error' });
            }
        }
    });

    $('div.herramienta-bloquear').click(function(){
        var tr = $(this).closest('tr');
        var block = (tr.hasClass('bloqueado_par') || tr.hasClass('bloqueado_impar')) ? false : true;
        var parametros = {
            bloqueado : block,
            id        : tr.attr('id')
        }
        window.callService('bloquearRegistro', parametros);

        if(window.rsService.soyError == false){
            if(block == false){
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
        }else{
            $.msgBox({ title: "Error", content: window.rsService.mensaje, type: 'error' });
        }
    });
    $('input[name=selectAll]').click(function(){
        var checked = $(this).attr('checked') ? true : false;
        if(checked){
            $('input[name=alertar]').attr('checked', 'checked');
        }else{
            $('input[name=alertar]').removeAttr('checked');
        }
    });
    $('div.herramienta-alertar').click(function(){
        var checked = false;
        var parametros = { idsToAlert : '' };

        $('input[name=alertar]').each(function(){
            if(checked == false){
                checked = $(this).attr('checked') ? true : false;
            }
        });
        if(checked){
            $.msgBox({
                title: "Confirmacion",
                content: "Que tipo de operacion desea realizar??",
                type: "confirm",
                buttons: [{ value: "Renovar" }, { value: "Custodia" }, { value: "Cancelar"}],
                success: function (result) {
                    result = result.toLowerCase();
                    if (result == "renovar" || result == "custodia" ) {
                        $('input[name=alertar]').each(function(){
                            var addId = $(this).attr('checked') ? true : false;
                            if(addId){
                                parametros.idsToAlert += $(this).closest('tr').attr('id')+',';
                            }
                        });
                        window.callService(result, parametros);

                        if(window.rsService.soyError == false){
                            $.msgBox({ title: "Informacion", content: window.rsService.mensaje, type: 'info' });

                            $.each(window.rsService.alertados, function(i, item) {
                                var tr = $('tr#'+i, 'div.detalles-table');

                                if(item.alertado == true){
                                    if(tr.hasClass('par')){
                                        tr.removeClass('par');
                                        tr.addClass('alertado_par');
                                    }else{
                                        tr.removeClass('impar');
                                        tr.addClass('alertado_impar');
                                    }
                                }else{
                                    if(tr.hasClass('par')){
                                        tr.removeClass('par');
                                        tr.addClass('FAIL_alertado_par');
                                    }else{
                                        tr.removeClass('impar');
                                        tr.addClass('FAIL_alertado_impar');
                                    }
                                }
                            });
                        }else{
                            $.msgBox({ title: "Error", content: window.rsService.mensaje, type: 'error' });
                        }

                    }
                }
            });
        }else{
            $.msgBox({ title: "Alerta", content: 'Debe seleccionar al menos un producto'});
        }

    });
    $('div.herramienta-imprimirDetalle').click(function(){
        var parametros = {
            tipo : window.parametros.tipo,
            id   : '<?=$_POST['id'];?>'
        }
        window.callService('imprimir', parametros);
        if(window.rsService.soyError === true){
            $.msgBox({ title: "Alerta", content: window.rsService.mensaje});
        }else{
            window.open(window.rsService.redirect);
        }
    });
</script>
