<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de NC</span>
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
                                <td class="field-name"><span class="field-name">Fecha</span></td>
                                <td class="field-input"><input type="text" class="datePicker" name="cobroFecha" /></td>
                            </tr> 
                            <tr>
                                <td class="field-name"><span class="field-name">Cuenta</span></td>
                                <td class="field-input"><input type="text" class="autocomplete required" name="relaciones"/></td>
                            </tr>
                            <tr style="height: 1px;">
                                <td style="height: 1px;" colspan="3"><hr style="color: green;"/></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <table class="parcial">
                                        <input id="errorParcial" type="hidden" value="0" />
                                        <tr>
                                            <td class="field-name"><span class="field-name">Forma de Pago</span></td>
                                            <td class="field-input"><input type="text" name="formas" class="autocomplete parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>            
                                        <tr>
                                            <td class="field-name"><span class="field-name">Importe</span></td>
                                            <td class="field-input"><input type="text" name="importe" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Comprobante</span></td>
                                            <td class="field-input"><input type="text" name="comprobante" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Remitos</span></td>
                                            <td class="field-input"><input type="text" name="facturas" class="autocomplete parcial required"/></td>
                                            <td class="addButton"></td>
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
                                <td class="field-input"><textarea type="text" name="observaciones" /></textarea></td>
                            </tr>        
                        </table>
                    </form>
                </td>
                
                <td class="secundary" style="display: none; width: 720px; vertical-align: top;">
                    <div id="productos">
                        <table id="productos" align="left" style="width: 700px; margin-left: 10px;">
                            <thead>
                                <tr>
                                    <th class="detalles-titulo-dark">Forma de pago</th>
                                    <th class="detalles-titulo-dark">Importe</th>
                                    <th class="detalles-titulo-dark">Comprobante</th>
                                    <th class="detalles-titulo-dark">Remito</th>
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
    <div class="modal_title"><span class="title">Resumen</span></div>
    
    <div class="modal_content">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td class="cabecera-title-blue">Fecha</td>
                    <td class="cabecera-detalle-blue cobroFecha"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Cuenta</td>
                    <td class="cabecera-detalle-blue relaciones"></td>
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
                    <th class="detalles-titulo-dark">Forma de Pago</th>
                    <th class="detalles-titulo-dark">Importe</th>
                    <th class="detalles-titulo-dark">Comprobante</th>
                    <th class="detalles-titulo-dark">Remitos</th>
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
        var tdForma = '<td params="'+$('input[name=formas]').attr('params')+'">'+$('input[name=formas]').val()+'</td>';
        var tdImp = '<td params="'+$('input[name=importe]').val()+'">'+$('input[name=importe]').val()+'</td>';
        var tdComp = '<td params="'+$('input[name=comprobante]').val()+'">'+$('input[name=comprobante]').val()+'</td>';
        var tdRemito = '<td params="'+$('input[name=facturas]').attr('params')+'">'+$('input[name=facturas]').val()+'</td>';
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
            
        tr = '<tr id="product_'+product_count+'" class="'+trClass+'">' + tdForma + tdImp + tdComp + tdRemito + tdSupr + '</tr>';
        trResumen = '<tr id="product_'+product_count+'" class="'+trClassResumen+'">' + tdForma + tdImp + tdComp + tdRemito + '</tr>';
        $('tbody', 'table#productos').prepend(tr);
        $('tbody', 'table#products_added').prepend(trResumen);
        window.added += 1;
        product_count += 1;
        resetForm();
                  
    }
    var resetForm = function(){
        $('input[name=bancos]').val('');
        $('input[name=bancos]').attr('params', '');
        $('input[name=importe]').val('');
        $('input[name=comprobante]').val('');
        $('input[name=chequeFecha]').val('');
        $('input[name=formas]').val('');
        $('input[name=formas]').attr('params', '');
        $('input[name=facturas]').val('');
        $('input[name=facturas]').attr('params', '');
        
    }
    var hacerAlta = function(){
        $('input#errorParcial', 'table.parcial').val('0');

        $('input.required', 'table.parcial').each(function(){
            window.validateInputs($(this), true);
        });    
        
        if($('input#errorParcial', 'table.parcial').val() == '0'){
            showSecundary();
        }        
    }
    // Validar y agregar productos a la lista en el modal de Alta de stock
    $('div.add-button.altaStock').click(function(){
        hacerAlta();
    });
    
    $('input[name=facturas]').live('keypress', function(e){
        var keyCode = e.which;    
        if(keyCode == 13){
            hacerAlta();
        }
    });
    
    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenAltaStock').click(function(){
        if(window.added < 1){
            $.msgBox({ title: "Alerta", content: 'Debe agregar al menos un producto'});
        }else{
            $('input#error', 'div#modal.inicio').val('0');
            
            $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
                window.validateInputs($(this));
            });
                        
            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('div#modal.inicio').hide();
                $('td.cabecera-detalle-blue.cobroFecha').html($('input[name=cobroFecha]').val());
                $('td.cabecera-detalle-blue.relaciones').html($('input[name=relaciones]').val());
                $('td.cabecera-detalle-blue.observaciones').html($('textarea[name=observaciones]').val());
                var movimiento;
                    switch($('select[name=tipoMovimientoId]').val()){
                        case 'A':
                            movimiento = 'Factura A';
                            break;
                        case 'B':
                            movimiento = 'Factura B';
                            break;
                    }
                    $('td.cabecera-detalle.movimiento').html(movimiento);

                $('div#modal.resumen').show();
            }
        }
    });    
    // valido que el input de cantidad solo permita numeros
    $('input[name=cantidad]').keypress(function (e) {
        var which = e.which;
        var keyCode = e.keyCode;
        // keyCode = 8 -> backspace || 9 -> tabulador 37 -> left || 39 -> right || 46 -> Suprimir || 116 -> F5
        // which 48 -> 57 son caracteres numericos
        if((which >= 48 && which <= 57) || keyCode == 8 || keyCode == 9 || keyCode == 37 || keyCode == 39 || keyCode == 46 || keyCode == 116){ 
            return true;
        }else{
            return false;
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
            cantidadProductos : added,
            desdeAlta         : false,
            detalle         : productosSplited,
            relacionId       : $('input[name=relaciones]').attr('params'),
            cobroFecha: $('input[name=cobroFecha]').val(),
            observaciones   : $('textarea[name=observaciones]').val(),
            tipo              : 'cobrosNc'
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
