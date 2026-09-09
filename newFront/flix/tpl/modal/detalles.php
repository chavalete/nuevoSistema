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
                                <?php
                                $params = '';
                                if (strstr($data['propiedades'][$i]['class'], 'autocomplete')) {
                                    $paramVal = !empty($data['propiedades'][$i]['id_value']) ? $data['propiedades'][$i]['id_value'] : $data['propiedades'][$i]['value'];
                                    $params = 'params="' . htmlspecialchars($paramVal) . '"';
                                }
                                ?>
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
                                        <?php
                                        $isAutocomplete = strstr($data['listadoDetalles']['modelo'][$e]['class'], 'autocomplete');
                                        $attrParams = '';
                                        if ($isAutocomplete) {
                                            // Se verifica si viene el ID específico desde el backend o se usa el texto
                                            if (isset($data['listadoDetalles']['celdas'][$i]['cell_params'][$e])) {
                                                $idVal = $data['listadoDetalles']['celdas'][$i]['cell_params'][$e];
                                            } else if (isset($data['listadoDetalles']['celdas'][$i]['cell_id'][$e])) {
                                                $idVal = $data['listadoDetalles']['celdas'][$i]['cell_id'][$e];
                                            } else {
                                                $idVal = $data['listadoDetalles']['celdas'][$i]['cell'][$e];
                                            }
                                            $attrParams = 'params="' . htmlspecialchars($idVal) . '"';
                                        }
                                        ?>
                                        <input class="required <?=$data['listadoDetalles']['modelo'][$e]['class'];?>" name="<?=$data['listadoDetalles']['modelo'][$e]['name']?>" value="<?=$data['listadoDetalles']['celdas'][$i]['cell'][$e];?>" <?=$attrParams;?> style="height: 17px; width: 90%;"/>
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
                    var attrParams = $(this).attr('params');
                    value = (attrParams && attrParams !== 'false' && attrParams.trim() !== '') ? attrParams : $(this).val().trim();
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

        $('tr.detalles-dato', 'div.detalles-table').removeClass('fila-editando');
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
        trContent.addClass('fila-editando');
    });

    $('div.editTool-detalle.cancel').click(function(){
        var trContent = $(this).closest('tr');
        $('div.detalle-input', trContent).hide();
        $('div.herramientasEdit-detalle', trContent).hide();
        $('div.detalle-dato', trContent).show();
        $('div.detalle-editar', trContent).show();
        trContent.removeClass('fila-editando');
    });

    // --- DETECTAMOS LA COLUMNA DE CANT_INGRESO POR PHP ---
    <?php
    $indexCantIngreso = -1;
    if (!empty($data['listadoDetalles']['modelo'])) {
        for($m=0; $m<count($data['listadoDetalles']['modelo']); $m++) {
            if($data['listadoDetalles']['modelo'][$m]['name'] == 'cant_ingreso') {
                $indexCantIngreso = $m;
                break;
            }
        }
    }
    ?>

    // --- AUTOMATIZACIÓN EN TIEMPO REAL (KEYUP & CHANGE) ---
    $(document).on('keyup change', 'div.detalles-table input[name="cant_recibida"], div.detalles-table input[name="cant_asoc"]', function(){
        if (window.parametros.tipo === 'ingresos') {
            var trContent = $(this).closest('tr');
            var inputRecibida = trContent.find('input[name="cant_recibida"]');
            var inputAsociada = trContent.find('input[name="cant_asoc"]');
            var inputIngreso  = trContent.find('input[name="cant_ingreso"]');

            if (inputRecibida.length && inputAsociada.length) {
                var cantRecibida = parseFloat(inputRecibida.val()) || 0;
                var cantAsociada = parseFloat(inputAsociada.val()) || 0;
                var totalIngreso = cantRecibida * cantAsociada;

                // 1. Si el input de ingreso existe (campo editable), le actualizamos el valor en vivo
                if (inputIngreso.length) {
                    inputIngreso.val(totalIngreso);
                }

                // 2. También actualizamos el div de texto plano por si es de solo lectura en pantalla
                var hasCheckbox = <?=(!empty($data['alertar'])) ? 'true' : 'false';?>;
                var colIndex = <?=$indexCantIngreso;?> + (hasCheckbox ? 1 : 0);
                var tdIngreso = trContent.find('td').eq(colIndex);
                if (tdIngreso.length) {
                    tdIngreso.find('div.detalle-dato').html(totalIngreso);
                }
            }
        }
    });

    $('div.editTool-detalle.acept').click(function(e){
        e.preventDefault(); // Previene interferencias de comportamiento de plugins terceros
        var value;
        $('input#error', 'div#modal.inicio').val('0');

        var trContent = $(this).closest('tr');

        // Validamos de manera específica los elementos obligatorios de este registro
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

            // --- ARMADO DE PARÁMETROS SEGÚN SECCIÓN ---
            if (window.parametros.tipo === 'ingreso') {
                // Capturamos TODOS los campos (editables y fijos)
                var hasCheckbox = <?=(!empty($data['alertar'])) ? 'true' : 'false';?>;

                // Mapeamos el listado del modelo que trajo PHP
                var modeloColumnas = <?=json_encode($data['listadoDetalles']['modelo']);?>;

                trContent.find('td').each(function(index) {
                    // Si hay un checkbox al principio, la primera celda del loop es el checkbox y la ignoramos
                    var colIndex = hasCheckbox ? index - 1 : index;

                    // Si el índice corresponde a un campo definido en nuestro modelo de columnas
                    if (modeloColumnas[colIndex] && modeloColumnas[colIndex].name !== 'herramienta') {
                        var colName = modeloColumnas[colIndex].name;
                        var cellInput = $(this).find('input');
                        var cellValue = '';

                        if (cellInput.length) {
                            // Si tiene un input (editable), tomamos su valor
                            if (cellInput.hasClass('autocomplete')) {
                                var attrParams = cellInput.attr('params');
                                cellValue = (attrParams && attrParams !== 'false' && attrParams.trim() !== '') ? attrParams : cellInput.val().trim();
                            } else {
                                cellValue = cellInput.val().trim();
                            }
                        } else {
                            // Si no tiene input (solo lectura), tomamos el texto estático del div
                            cellValue = $(this).find('div.detalle-dato').text().trim();
                        }

                        params.campos[colName] = cellValue;
                    }
                });

                // Doble chequeo por seguridad de la matemática de cant_ingreso
                if (params.campos['cant_recibida'] !== undefined && params.campos['cant_asoc'] !== undefined) {
                    var cantRecibida = parseFloat(params.campos['cant_recibida']) || 0;
                    var cantAsociada = parseFloat(params.campos['cant_asoc']) || 0;
                    params.campos['cant_ingreso'] = cantRecibida * cantAsociada;
                }

            } else {
                // MODO ORIGINAL: Mapeamos solo los inputs editables existentes en la fila
                $('input', trContent).each(function(){
                    var inputName = $(this).attr('name');

                    if(!inputName) return true;

                    if($(this).hasClass('autocomplete')){
                        var attrParams = $(this).attr('params');
                        value = (attrParams && attrParams !== 'false' && attrParams.trim() !== '') ? attrParams : $(this).val().trim();
                    }else{
                        value = $(this).val().trim();
                    }

                    params.campos[inputName] = value;
                });
            }

            window.callService(service, params);

            if(window.rsService.soyError == false){
                // Actualizamos visualmente las celdas editables con los inputs
                $('input', trContent).each(function(){
                    if($(this).attr('name')) {
                        var td = $(this).closest('td');
                        $('div.detalle-dato', td).html($(this).val());
                    }
                });

                // Si cant_ingreso no era editable, nos aseguramos de plasmar el total calculated en pantalla
                if (window.parametros.tipo === 'ingreso' && !trContent.find('input[name="cant_ingreso"]').length) {
                    var hasCheckbox = <?=(!empty($data['alertar'])) ? 'true' : 'false';?>;
                    var colIndex = <?=$indexCantIngreso;?> + (hasCheckbox ? 1 : 0);
                    var tdIngreso = trContent.find('td').eq(colIndex);
                    if (tdIngreso.length && params.campos['cant_ingreso'] !== undefined) {
                        tdIngreso.find('div.detalle-dato').html(params.campos['cant_ingreso']);
                    }
                }

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
