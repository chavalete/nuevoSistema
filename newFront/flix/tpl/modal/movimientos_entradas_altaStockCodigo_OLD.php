<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de stock</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="altaStockCodigo" style="top: 30px; left: -300px; width: 320px; height: 250px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->        
    </div>
    <div class="modal_content">
        <table>
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="primary">
                    <table>
			<tr>
                            <td class="field-name"><span class="field-name">Proveedor</span></td>
                            <td class="field-input"><input type="text" class="autocomplete" name="proveedores" /></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Codigos</span></td>
                            <td class="field-input"><input type="text" name="codigos"/></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Producto</span></td>
                            <td class="field-input"><input type="text" class="autocomplete" name="productos" /></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Estanteria</span></td>
                            <td class="field-input"><input type="text" class="autocomplete required" name="estanteriasEntradasCodigos" /></td>
                        </tr>    
                        <tr>
                            <td class="field-name"><span class="field-name">Nro. Remito</span></td>
                            <td class="field-input"><input type="text" name="remitos" /></td>
                        </tr>                
                        <tr>
                            <td class="field-name"><span class="field-name">Nro. Factura</span></td>
                            <td class="field-input"><input type="text" name="facturas"/></td>
                        </tr>                
                        <tr>
			    <td class="field-name"><span class="field-name">Observaciones</span></td>
			    <td class="field-input"><textarea type="text" name="observaciones"/></td>
			</tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Tipo movimiento</span></td>
                            <td class="field-input">
                                <select name="tipoMovimientoId">
                                    <option value="0">Seleccionar</option>
                                    <option value="1">Entradas</option>
                                    <option value="3">Devolucion</option>
                                    <option value="4">Traspaso</option>
                                </select>
                            </td>
                        </tr>            
                    </table>
                </td>
                <td class="secundary" style="display: none; width: 360px; vertical-align: top;">
                    <div id="codigos">
                        <table id="codigos" align="left" style="width: 340px;">
                            <thead>
                                <tr>
                                    <th class="detalles-titulo">Codigo</th>
                                    <th class="detalles-titulo">Descripcion</th>
                                    <th class="detalles-titulo"></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
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
    <div class="modal_title"><span class="title">Alta de stock - Resumen</span></div>
    
    <div class="modal_content">
        <div>
            <table style="width: 100%;">
		<tr>
                    <td class="cabecera-title">Proveedor</td>
                    <td class="cabecera-detalle proveedores"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Nro. Remito</td>
                    <td class="cabecera-detalle remito"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Nro. Factura</td>
                    <td class="cabecera-detalle factura"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Observaciones</td>
                    <td class="cabecera-detalle observaciones"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Tipo movimiento</td>
                    <td class="cabecera-detalle movimiento"></td>
                </tr>                
            </table>
            
        </div>
        
        <div style="margin-top: 10px; width: 100%; height: 165px; overflow: auto;">
            <table id="products_added" style="width: 100%;">
                <thead>
                    <th class="detalles-titulo">Producto</th>
                    <th class="detalles-titulo">Descripcion</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <hr class="modal-divisor" />
        <div class="send-button volver">Volver</div>        
        <div class="send-button hacerAltaStock">Hacer alta</div>        
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var counter  = Number(0);    
        window.added = Number(0);

        // MOSTRAR RESUMEN DE CONFIRMACION
        $('div.send-button.resumenAltaStock').click(function(){
            if(window.added < 1){
                $.msgBox({ title: "Alerta", content: 'Debe agregar al menos un producto'});
            }else{
                // Reseteo los errores antes de verificar
                $('input#error', 'div#modal.inicio').val('0');

                $('input.required', 'div#modal.inicio').each(function(){
                    window.validateInputs($(this));
                });

                if($('input#error', 'div#modal.inicio').val() == '0'){
                    $('div#modal.inicio').hide();

                    $('td.cabecera-detalle.estanteria').html($('input[name=estanteriasEntradasCodigos]').val());
                    $('td.cabecera-detalle.productos').html($('input[name=productos]').val());
                    $('td.cabecera-detalle.proveedores').html($('input[name=proveedores]').val());
                    $('td.cabecera-detalle.remito').html($('input[name=remitos]').val());
                    $('td.cabecera-detalle.factura').html($('input[name=facturas]').val());
                    $('td.cabecera-detalle.observaciones').html($('input[name=observaciones]').val());
                    var movimiento;
                    switch($('select[name=tipoMovimientoId]').val()){
                        case '1':
                            movimiento = 'Entradas';
                            break;
                        case '3':
                            movimiento = 'Devolucion';
                            break;
                        case '4':
                            movimiento = 'Traspaso';
                            break;
                    }
                    $('td.cabecera-detalle.movimiento').html(movimiento);
                    $('div#modal.resumen').show();
                }
            }

        });
        // VOLVER
        $('div.send-button.volver').click(function(){
            $('div#modal.resumen').hide();
            $('div#modal.inicio').show();
        });     

        $('input[name=codigos]', 'div#modal').keypress(function(e){
            if(e.keyCode == 13 && $(this).val().trim() != ''){
                counter += 1;
                window.added += 1;
                if($('td.secundary').css('display') == 'none'){
                    $.colorbox.resize({
                        width: '800'
                    });
                    $('td.secundary').show();
                }

                var trClass = 'detalles-dato';
                var trClassResumen = 'detalles-dato';
                var tdCod = '<td class="codigo_inserted">'+$(this).val()+'</td>';
                var tdDes = '<td id="codigo_'+counter+'"></td>'
                var tdSup = '<td><div class="eliminar" title="Quitar"></div></td>';
                var tr;
                var trResumen;

                if($('tr', 'table#codigos tbody').first().hasClass('par')){
                    trClass += ' impar';
                }else if($('tr', 'table#codigos tbody').first().hasClass('impar')){
                    trClass += ' par';
                }else{
                    trClass += ' impar';
                }
                if($('tr', 'table#products_added tbody').first().hasClass('par')){
                    trClassResumen += ' impar';
                }else if($('tr', 'table#products_added tbody').first().hasClass('impar')){
                    trClassResumen += ' par';
                }else{
                    trClassResumen += ' impar';
                }            

                tr = '<tr id="codigo_'+counter+'" class="'+trClass+'">' + tdCod + tdDes + tdSup + '</tr>';
                trResumen = '<tr id="codigo_'+counter+'" class="'+trClassResumen+'">' + tdCod + tdDes+ '</tr>';

                $('tbody', 'table#codigos').prepend(tr);
                $('tbody', 'table#products_added').prepend(trResumen);

                $.post(
                    'inc/process.php',
                    {
                        accion: 'lectorCodigo',
                        parametros: { codigo: $(this).val() }
                    }, 
                    function(data){
                        var td = 'td#codigo_'+counter;
                        $(td).html(data.descripcion);
                    },
                    'json'
                );
                $(this).val('');
            }

        });
        $('table#codigos').delegate('div.eliminar', 'click', function(){
            var idProducto = $(this).closest('tr').attr('id');

            $(this).closest('tr').remove();
            $('tr#'+idProducto , 'table#products_added').remove();

            window.added -= 1;

            $('tr', 'table#codigos, table#products_added').each(function(index){
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
        // REALIZAR ALTA
        $('div.hacerAltaStock').click(function(){
            var codigosSplited = '';
            $('td.codigo_inserted', 'table#codigos tbody').each(function(){
                codigosSplited += $(this).html();
                codigosSplited   += '||';
            });
            var params = {
                codigos           : codigosSplited,
                desdeAlta         : false,
                estanterias       : $('input[name=estanteriasEntradasCodigos]').attr('params'),
                productoId       : $('input[name=productos]').attr('params'),
                proveedores       : $('input[name=proveedores]').attr('params'),
                facturas          : $('input[name=facturas]').val(),
                observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                remitos           : $('input[name=remitos]').val(),
                tipoMovimientoId  : $('select[name=tipoMovimientoId]').val(),
                tipo              : 'entradasCodigos'
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

                    if(!response.soyError){
                        $('div#modal').css('opacity', '1');
                        $.colorbox.close();
                        window.loadFlix();
                    }else{
                        $('div#modal').css('opacity', '1');
                    }
                }
            );
        });   
    });
    
</script>
