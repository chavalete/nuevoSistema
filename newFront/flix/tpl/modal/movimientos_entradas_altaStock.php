<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de stock</span>
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
                                <td class="field-name"><span class="field-name">Proveedor</span></td>
                                <td class="field-input"><input type="text" class="autocomplete required" name="proveedores"/></td>
                                <td class="addButton"><div class="addButton crear-proveedores" title="Crear proveedor"></div></td>
                            </tr>

                            <tr style="height: 1px;">
                                <td style="height: 1px;" colspan="3"><hr style="color: green;"/></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <table class="parcial">
                                        <input id="errorParcial" type="hidden" value="0" />
                                        <tr>
                                            <td class="field-name"><span class="field-name">Productos</span></td>
                                            <td class="field-input"><input type="text" class="autocomplete parcial required" name="productos"/></td>
                                            <td class="addButton"><div class="addButton crear-productos" title="Crear producto"></div></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Cantidad</span></td>
                                            <td class="field-input"><input type="text" name="cantidad"  id="cantidad" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Costo</span></td>
                                            <td class="field-input"><input type="text" name="productoPcosto" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Vencimiento</span></td>
                                            <td class="field-input"><input type="text" class="datePicker parcial required" name="vencimiento"/></td>
                                        </tr>
                                    </table>
                                <td>
                            </tr>
                            <tr>
                                <td />
                                <td><div class="add-button altaStock">Agregar</div></td>
                                <td />
                            </tr>

                            <tr style="height: 1px;"><td style="height: 1px;" colspan="3"><hr/></td></tr>
                            <tr>
                                <td class="field-name"><span class="field-name">Nro. Remito</span></td>
                                <td class="field-input"><input type="text" class="required" name="remitos" /></td>
                                <td class="addButton"></td>
                            </tr>
                            <tr>
                                <td class="field-name"><span class="field-name">Tipo Comprobante</span></td>
                                <td class="field-input">
                                    <select name="tipoComprobante">
                                        <option value="0">Seleccionar</option>
                                        <option value="Factura A">Factura A</option>
                                        <option value="Remito X">Remito X</option>
                                    </select>
                                </td>
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
                                    <th colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</th>
                                    <th colspan="2" class="total" style="text-align: left; padding-leftt: 10px; color: red"></th>
                                </tr>
                                <tr>
                                    <th class="detalles-titulo-dark">Producto</th>
                                    <th class="detalles-titulo-dark">Cantidad</th>
                                    <th class="detalles-titulo-dark">Costo</th>
                                    <th class="detalles-titulo-dark">Vencimiento</th>
                                    <th class="detalles-titulo-dark"></th>
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
    <div class="modal_title"><span class="title">Alta de stock - Resumen</span></div>

    <div class="modal_content">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td class="cabecera-title-blue">Proveedor</td>
                    <td class="cabecera-detalle-blue proveedor"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Nro. Remito</td>
                    <td class="cabecera-detalle-blue remito"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Tipo Comprobante</td>
                    <td class="cabecera-detalle-blue tipoComprobante"></td>
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
                    <th class="detalles-titulo-dark">Producto</th>
                    <th class="detalles-titulo-dark">Cantidad</th>
                    <th class="detalles-titulo-dark">Costo</th>
                    <th class="detalles-titulo-dark">Vencimiento</th>
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
        var tdProd = '<td params="'+$('input[name=productos]').attr('params')+'">'+$('input[name=productos]').val()+'</td>';
        var tdCant = '<td params="'+$('input[name=cantidad]').val()+'"><input type="number" style="width: 100px;" class="editableCount" value="'+$('input[name=cantidad]').val()+'" /></td>';
        var tdCantResumen = '<td class="cant_resument">'+$('input[name=cantidad]').val()+'</td>';
        var tdCosto = '<td params="'+$('input[name=productoPcosto]').val()+'"><input type="number" style="width: 100px;" class="editablePrice" value="'+$('input[name=productoPcosto]').val()+'" /></td>';
        var tdCostoResumen = '<td class="costo_resumen">'+$('input[name=productoPcosto]').val()+'</td>';
        var tdCosto2 = '<td class="pt">'+(parseInt($('input[name=productoPcosto]').val()) * parseInt($('input[name=cantidad]').val()))+'</td>';
        var tdVenc = '<td params="'+$('input[name=vencimiento]').val()+'">'+$('input[name=vencimiento]').val()+'</td>';
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

        tr = '<tr id="product_'+product_count+'" class="'+trClass+'">' + tdProd + tdCant +  tdCosto + tdVenc + tdCosto2 + tdSupr + '</tr>';
        trResumen = '<tr id="product_'+product_count+'" class="'+trClassResumen+'">'  + tdProd + tdCantResumen + tdVenc  + tdCostoResumen  + '</tr>';
        $('tbody', 'table#productos').prepend(tr);
        $('tbody', 'table#products_added').prepend(trResumen);
        window.added += 1;
        product_count += 1;
        resetForm();
        calculateTotal();

    }

    var calculateTotal = function(){
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        });
        var total = 0;
        $('tr', 'table#productos tbody').each(function(){
            total += parseInt($('td.pt', $(this)).text());
        });

        $('th.total', 'table#productos thead').text(formatter.format(total));
    }

    $('input.editableCount, input.editablePrice', 'div#productos').live('change', function(){
        var tr = $(this).closest('tr');
        var p_id = tr.attr('id');
        var total = parseInt($('input.editableCount', tr).val()) * parseInt($('input.editablePrice', tr).val());
        $(this).closest('td').attr('params', $(this).val());

        $('td.pt', tr).text(total);
        $('td.cant_resument', 'table#products_added tr#' + p_id).text($('input.editableCount', tr).val());
        $('td.costo_resumen', 'table#products_added tr#' + p_id).text(total);
        calculateTotal();
    });

    var resetForm = function(){
        $('input[name=productos]').val('');
        $('input[name=productos]').attr('params', '');
        $('input[name=cantidad]').val('');
        $('input[name=lotes]').val('');
        $('input[name=vencimiento]').val('');
        $('input[name=productos]').focus();
        $('input[name=productoPcosto]').val('');
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
    $('input[name=productoPcosto]').live('keypress', function(e){
        var keyCode = e.which;
        if(keyCode == 13){
            hacerAlta();
        }
    });

    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenAltaStock').click(function(){

            $('input#error', 'div#modal.inicio').val('0');

            $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
                window.validateInputs($(this));
            });

            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('div#modal.inicio').hide();

                $('td.cabecera-detalle-blue.proveedor').html($('input[name=proveedores]').val());
                $('td.cabecera-detalle-blue.remito').html($('input[name=remitos]').val());
                $('td.cabecera-detalle-blue.tipoComprobante').html($('select[name=tipoComprobante]').val());
                $('td.cabecera-detalle-blue.observaciones').html($('textarea[name=observaciones]').val());
                $('div#modal.resumen').show();
            }

    });
    // valido que el input de cantidad solo permita numeros
    $('input[name=cantidad]').keypress(function (e) {
        var which = e.which;
        var keyCode = e.keyCode;
        // keyCode = 8 -> backspace || 9 -> tabulador 37 -> left || 39 -> right || 46 -> Suprimir || 116 -> F5
        // which 48 -> 57 son caracteres numericos
        if((which >= 48 && which <= 57) || keyCode == 8 || keyCode == 9 || keyCode == 37 || keyCode == 39 || keyCode == 46 || keyCode == 116 || keyCode == 13){
            return true;
        }else{
            return false;
        }
    });
    var cargarCosto = function(datos){
        var form = $('form.modal', 'div#modal');
        $('input[name=productoPcosto]', form).val(typeof(datos.productoPcosto) == 'undefined' ? '' : datos.productoPcosto);
    }
    var vaciarCosto = function(){
        var form = $('form.modal', 'div#modal');
        $('input[name=productoPcosto]', form).val('');
    }


    // Ajax para cargar la direccion del cliente dinamicamente
    $("#cantidad").blur(function() {
        if($(this).val() != 1){
            {
                $.post(
                    'inc/process.php',
                    {
                        accion  : 'traerDatos',
                        id      : $('input[name=productos]').attr('params'),
                        tipo    : 'productos'
                    },
                    function(data){
                        if(data.soyError == false){
                            cargarCosto(data);
                        }else{
                            $.msgBox({ title: "Error", content: 'No se pudo cargar el costo'});
                        }
                    },
                    'json'
                );
            }
        }else{
            vaciarCosto();
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
            estanterias       : $('input[name=estanterias]').attr('params'),
            facturas          : $('input[name=facturas]').val(),
            fechaVencimiento  : $('input[name=fechaDesde]').val(),
            lotes             : $('input[name=lotes]').val(),
            nroTramite    : $('input[name=nroTramite]').val(),
            productos         : productosSplited,
            proveedores       : $('input[name=proveedores]').attr('params'),
            remitos           : $('input[name=remitos]').val(),
            tipoComprobante      : $('select[name=tipoComprobante]').val(),
            despacho          : $('input[name=despacho]').val(),
            observaciones   : $('textarea[name=observaciones]').val(),
            tipo              : 'entradas'
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
