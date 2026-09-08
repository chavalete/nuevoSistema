<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta Modelos</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position" style='right: 30px; float: right;'>
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="altaStock" style="top: 30px; left: -300px; width: 320px; height: 365px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->
    </div>
    
    <div class="modal_content">
        <table>
            <tr>
                <td class="primary">
                    <form class="modal">
                        <input id="error" type="hidden" value="0" />
                        <table>
                            <tr>
                                <td class="field-name"><span class="field-name">Modelo</span></td>
                                <td class="field-input"><input type="text" class="autocomplete" name="modelos" /></td>
                            </tr>
                            <tr>
                                <td class="field-name"><span class="field-name">Modelo nuevo</span></td>
                                <td class="field-input"><input type="text" name="modeloNuevo" /></td>
                            </tr>
                            <tr style="height: 1px;">
                                <td style="height: 1px;" colspan="3"><hr style="color: green;"/></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <table class="parcial">
                                        <input id="errorParcial" type="hidden" value="0" />
                                                    
                                        <tr>
                                            <td class="field-name"><span class="field-name">Display</span></td>
                                            <td class="field-input"><input type="text" name="display" class="parcial required"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Nombre</span></td>
                                            <td class="field-input"><input type="text" class="parcial required" name="nombre"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Orden</span></td>
                                            <td class="field-input"><input type="text" name="ordenNro" class="parcial required"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Clase</span></td>
                                            <td class="field-input">
                                            <select name="clase">
                                            <option value="">Seleccionar</option>
                                            <option value="select">Select</option>
                                            <option value="autocomplete">Autocomplete</option>
                                            <option value="null">Sin clase</option>
                                            </select>
                                        </td>
                                        </tr>
                                    </table>
                                <td>
                            </tr>
                            <tr>
                                <td />
                                <td><div class="add-button altaStock">Agregar</div></td>
                                <td />
                            </tr>
                            
                            
                            <tr>
                            <td class="field-name"><span class="field-name">Observaciones</span></td>
                                <td class="field-input"><textarea type="text" name="observaciones"></textarea></td>
                            </tr>
                        </table>
                    </form>
                </td>
                
                <td class="secundary" style="display: none; width: 720px; vertical-align: top;">
                    <div id="productos">
                        <table id="productos" align="left" style="width: 700px; margin-left: 10px;">
                            <thead>
                                <tr>
                                    <th class="detalles-titulo-dark">Display</th>
                                    <th class="detalles-titulo-dark">Nombre</th>
                                    <th class="detalles-titulo-dark">Órden</th>
                                    <th class="detalles-titulo-dark">Clase</th>
                                    <th class="detalles-titulo-dark eliminar" style="width: 15px;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </td>                
            </tr> 
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button resumenAltaStock">Confirmar</div>
    </div>
</div>

<div id="modal" class="resumen" style="display: none">
    <div class="modal_title"><span class="title">Alta de stock - Modelos</span></div>
    
    <div class="modal_content">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td class="cabecera-title-blue">Modelo</td>
                    <td class="cabecera-detalle-blue modelos"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Modelo nuevo</td>
                    <td class="cabecera-detalle-blue modeloNuevo"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Observaciones</td>
                    <td class="cabecera-detalle-blue observaciones"></td>
                </tr>
            </table>
            
        </div>
        
        <div style="margin-top: 10px; width: 100%; height: 395px; overflow: auto;">
            <table id="products_added" style="width: 100%;">
                <thead>
                    <th class="detalles-titulo-dark">Display</th>
                    <th class="detalles-titulo-dark">Nombre</th>
                    <th class="detalles-titulo-dark">Órden</th>
                    <th class="detalles-titulo-dark">Clase</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <hr class="modal-divisor" />
        <div class="send-button volver">Volver</div>        
        <div class="send-button hacerAltaStock">Hacer alta</div>        
    </div>
</div>

<div id="modal" class="nextStep" style="display: none;">
    <div class="modal_title"><span class="title"></span></div>
    
    <div class="modal_content"></div>
    <hr class="modal-divisor" />
    <div class="send-button volver-abm">Volver</div>  
    <div class="send-button crear-volver">Crear y volver</div>    
</div>

<script type="text/javascript" src="js/modal-nextStep.js"></script>
<script type="text/javascript">
    $('div.eliminar', 'table#productos').die();
    $('div.add-button.altaStock').die();
    nextStep.Init();
    
    window.added = Number(0);
    var product_count = Number(0);
    
    var showSecundary = function(){
        if($('td.secundary').css('display') == 'none'){
            $.colorbox.resize({width: '1200'});
            $('td.secundary').show();
        }
            
        var trClass = 'detalles-dato-blue';
        var trClassResumen = 'detalles-dato-blue';
        
        var tdDisp = '<td params="'+$('input[name=display]').val()+'">'+$('input[name=display]').val()+'</td>';
        var tdNom = '<td params="'+$('input[name=nombre]').val()+'">'+$('input[name=nombre]').val()+'</td>';
        var tdOrd = '<td params="'+$('input[name=ordenNro]').val()+'">'+$('input[name=ordenNro]').val()+'</td>';
        var tdCla = '<td params="'+$('select[name=clase]').val()+'">'+$('select[name=clase]').val() +'</td>';
        
        var tdSupr = '<td class="eliminar"><div class="eliminar" title="Quitar"></div></td>';
        var tr;
        var trResumen;

        if($('tr', 'table#productos tbody').first().hasClass('par')){
            trClass += ' impar';
        }else if($('tr', 'table#productos tbody').first().hasClass('impar')){
            trClass += ' par';
        }else{
            trClass += ' impar';
        }
        if($('tr', 'table#products_added tbody').first().hasClass('par')){
            trClassResumen += ' impar';
        }else if($('tr', 'table#productos tbody').first().hasClass('impar')){
            trClassResumen += ' par';
        }else{
            trClassResumen += ' impar';
        }
            
        tr = '<tr id="product_'+product_count+'" class="'+trClass+'">' + tdDisp + tdNom + tdOrd + tdCla + tdSupr + '</tr>';
        trResumen = '<tr id="product_'+product_count+'" class="'+trClassResumen+'">' + tdDisp + tdNom + tdOrd + tdCla + '</tr>';
        $('tbody', 'table#productos').prepend(tr);
        $('tbody', 'table#products_added').prepend(trResumen);
        window.added += 1;
        product_count += 1;
        resetForm();
                  
    };
    var resetForm = function(){
        $('input[name=display]').val('');    
        $('input[name=nombre]').val('');
        $('input[name=ordenNro]').val('');    
        $('select[name=clase]').val('');
        $('input[name=display]').focus();

    }
    var hacerAlta = function(){
        $('input#errorParcial', 'table.parcial').val('0');
        $('input#error', 'table.parcial').removeClass('error');

        $('input.required', 'table.parcial').each(function(){
            window.validateInputs($(this), true);
        });    
        
        if($('input#errorParcial', 'table.parcial').val() == '0'){
            showSecundary();
            $('input[name=productosCant]').focus();
        }        
    };
    
    
    
    // Validar y agregar productos a la lista en el modal de Alta de stock
    $('div.add-button.altaStock').click(function(){
        hacerAlta();
    });
    $('input[name=lote]').keypress(function(e){
        if(e.which == 13){
            $('div.add-button.altaStock', $(this).closest('td.primary')).click();
        }
    });
    
    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenAltaStock').click(function(){
        if(window.added < 1){
            $.msgBox({ title: "Alerta", content: 'Debe agregar al menos un detalle'});
        }else{
            $('input#error', 'div#modal.inicio').val('0');
            
            $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
                window.validateInputs($(this));
            });
                        
            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('div#modal.inicio').hide();

                $('td.cabecera-detalle-blue.modelos').html($('input[name=modelos]').val());
                $('td.cabecera-detalle-blue.modeloNuevo').html($('input[name=modeloNuevo]').val());
                $('td.cabecera-detalle-blue.display').html($('input[name=display]').val());
                $('td.cabecera-detalle-blue.nombre').html($('input[name=nombre]').val());
                $('td.cabecera-detalle-blue.ordenNro').html($('input[name=ordenNro]').val());
                $('td.cabecera-detalle-blue.clase').html($('select[name=clase]').val());
                $('td.cabecera-detalle-blue.observaciones').html($('textarea[name=observaciones]').val());

                $('div#modal.resumen').show();
            }
        }
    });    
 
    // Elimino elementos agregados
    $('div.eliminar', 'table#productos').live('click', function(){
        var idProducto = $(this).closest('tr').attr('id');
        window.added -= 1;
        
        $(this).closest('tr').remove();
        $('tr#'+idProducto , 'table#products_added').remove();
        
        $('tr', 'table#productos, table#products_added').each(function(index){
            if(index > 0){
                if(index % 2){
                    if($(this).hasClass('par')){
                        $(this).removeClass('par');
                        $(this).addClass('impar');
                    }
                }else{
                    if($(this).hasClass('impar')){
                        $(this).removeClass('impar');
                        $(this).addClass('par');
                    }
                }
            }
        });
    });

    // VOLVER
    $('div.send-button.volver').click(function(){
        $('div#modal.resumen').hide();
        $('div#modal.inicio').show();
    });  
    // REALIZAR ALTA
    $('div.hacerAltaStock').click(function(){
        var productosSplited = '';
        $('tr', 'table#productos tbody').each(function(){
            $('td', this).each(function(){
                if(typeof $(this).attr('params') != 'undefined' && $(this).attr('params').trim() != ''){
                    productosSplited += $(this).attr('params');
                    productosSplited += '#';
                }
            });
            productosSplited += '||';
        });
        var params = {
            cantidadModelos     : added,
            desdeAlta           : false,
            modelos             : $('input[name=modelos]').attr('params'),
            modeloNuevo         : $('input[name=modeloNuevo]').val(),
            observaciones       : $('textarea[name=observaciones]').val(),
            detalles            : productosSplited,
            tipo                : 'modelos'
        };
        $('div#modal').css('opacity', '0.4');
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : params
            }, 
            function(data){
                var response = JSON.parse(data);
                $.msgBox({ title: "Alerta", content: response.mensaje});
                
                $('div#modal').css('opacity', '1');
                
                if(!response.soyError){
                    $.colorbox.close();
                    window.loadFlix();
                }
            }
        );
    });
</script>


