<div id="modal" class="inicio bajaStock">
    <div class="modal_title">
        <span class="title">Baja de stock</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position" style='right: 30px; float: right;'>
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="salidas" style="top: 30px; left: -300px; width: 320px; height: 365px;"></div>
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
                                <td class="field-name"><span class="field-name">Forma de pago</span></td>
                                <td class="field-input"><input type="text" class="autocomplete required" name="listaDePrecios"/></td>
                            </tr>
                            <tr>
                                <td class="field-name"><span class="field-name">Cliente</span></td>
                                <td class="field-input"><input type="text" class="autocomplete required" name="clientes"/></td>
                                <td class="addButton">
                                    <?php if(empty($_GET['id'])) { ?>
                                        <div class="addButton crear-clientes" title="Crear cliente"></div>
                                    <?php } ?>
                                </td>
                            </tr>

                            <tr style="height: 1px;">
                                <td style="height: 1px;" colspan="3"><hr style="color: green;"/></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <table class="parcial">
                                        <input id="errorParcial" type="hidden" value="0" />
                                        <tr>
                                            <td class="field-name"><span class="field-name">Producto</span></td>
                                            <td class="field-input"><input type="text" class="autocomplete parcial required" name="productosCant" loading="1" id="productosCant"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Cantidad</span></td>
                                            <td class="field-input"><input type="text" name="cantidadP" class="parcial" value="1"/></td>
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

                            <tr style="height: 1px;"><td style="height: 1px;" colspan="3"><hr/></td></tr>
                            <tr>
                                <td class="field-name"><span class="field-name">Tipo de Entrega</span></td>
                                <td class="field-input">
                                    <select name="tipoEntrega">
                                        <option value="Mostrador">Mostrador</option>
                                        <option value="Envio">Envio</option>
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
                                    <th class="remitoNro" style="text-align: left; color: gray; font-weight: normal;"></th>
                                    <th colspan="2" style="text-align: right; padding-right: 10px;">TOTAL</th>
                                    <th colspan="2" class="total" style="text-align: left; padding-left: 10px; color: red"></th>
                                </tr>
                                <tr>
                                    <th class="detalles-titulo-dark">Producto</th>
                                    <th class="detalles-titulo-dark">Cantidad</th>
                                    <th class="detalles-titulo-dark">Precio U</th>
                                    <th class="detalles-titulo-dark">Precio T</th>
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
    <div class="modal_title"><span class="title"> Baja de Stock - Resumen</span></div>

    <div class="modal_content">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td class="cabecera-title-blue">Cliente</td>
                    <td class="cabecera-detalle-blue clientes"></td>
                </tr>
                <tr>
                    <td class="cabecera-title-blue">Tipo Entrega</td>
                    <td class="cabecera-detalle-blue tipoEntrega"></td>
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
                    <tr>
                        <th colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</th>
                        <th colspan="2" class="total" style="text-align: left; padding-left: 10px; color: red"></th>
                    </tr>
                    <tr>
                        <th class="detalles-titulo-dark">Producto</th>
                        <th class="detalles-titulo-dark">Cantidad</th>
                        <th class="detalles-titulo-dark">Precio U</th>
                        <th class="detalles-titulo-dark">Precio T</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <hr class="modal-divisor" />
        <div class="send-button volver">Volver</div>
        <div class="send-button hacerAltaStock">Hacer baja</div>
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
    var error = false;

    var preLoadData = function(){
        $.post(
            'inc/process.php',
            {
                accion: 'traerDatosMovimiento',
                id: <?=!empty($_GET['id'])?$_GET['id']:0;?>,
                tipo: 'salidas'
            },
            function(response){
                response = JSON.parse(response);

                $("input[name=clientes]")
                    .attr("params", response.cliente.id)
                    .attr("idlistaprecio", response.cliente.idListaPrecio)
                    .attr("title", response.cliente.label)
                    .val(response.cliente.label)
                    .prop("disabled", true);

                $("select[name=tipoEntrega]").val(response.tipoEntrega);
                $("textarea[name=observaciones]").val(response.observaciones);
                $("th.remitoNro").text('Remito: ' + response.nroRemito)
                $("input[name=listaDePrecios]")
                    .attr("params", response.lista.id)
                    .attr("title", response.lista.label)
                    .val(response.lista.label)


                $.each(response.producto, function(i, v){
                    if(v.idFamilia == null){
                        v.idFamilia = 'undefined';
                    }
                    window.added += 1;
                    product_count += 1;
                    drawProduct(v);

                });
                calculateTotal();
                resetForm();
                $('td.secundary').show();
            }
        );
    }

    <?php if(!empty($_GET['id'])){ ?>
        preLoadData();
    <?php } ?>

    var showSecundary = function(){
        console.log('BIND:: showSecundary');

        // Evito multiples entradas con error
        if(error){
            return false;
        }

        if(!$('input[name=listaDePrecios]').attr('params')){
            error = true;
            $.msgBox({
                title: "Alerta",
                content: 'Debe ingresar una forma de pago antes de agregar productos.',
                afterClose: function(){
                    error = false;
                }
            });
            return false;
        }

        var current_id_p = $('input[name=productosCant]').attr('params');
        var input_cant = parseInt($('input[name=cantidadP]').val()) || 0;
        var existing_tr = null;

        // Buscamos si el producto ya existe en la tabla
        $('tr', 'table#productos tbody').each(function(){
            if(current_id_p == $(this).attr('id_p')){
                existing_tr = $(this);
            }
        });

        var id_f = $('input[name=productosCant]').attr('idfamilia');

        if(existing_tr !== null) {
            // SI YA EXISTE: Sumamos cantidades al elemento existente
            var current_cant = parseInt($('.editableCount', existing_tr).val()) || 0;
            var nueva_cantidad = current_cant + input_cant;

            // Actualizamos visualmente el input y los atributos de la fila
            $('.editableCount', existing_tr).val(nueva_cantidad);
            existing_tr.attr('cantidad', nueva_cantidad);
            existing_tr.closest('td').attr('cantidad', nueva_cantidad);
            existing_tr.attr('params', nueva_cantidad);

            // Forzamos el recalculo de la promo/precios simulando el cambio del elemento existente
            var rs = reCalculatePromo(id_f, true, existing_tr);

            $('td.pu', existing_tr).text(rs.precio);
            var pt = nueva_cantidad * rs.precio;
            $('td.pt', existing_tr).attr('value', pt).text(pt);

            // Si pertenece a una familia, actualizamos los otros productos hermanos de esa familia
            if(id_f != 'undefined'){
                $('tr', 'table#productos tbody').each(function(){
                    var tr = $(this);
                    if($(this).attr('id_f') == id_f){
                        $('td.pu', tr).text(rs.precio);
                        $('td.pt', tr)
                            .attr('value', rs.precio * $('.editableCount', tr).val())
                            .text(rs.precio * $('.editableCount', tr).val());
                    }
                });
            }
        } else {
            // SI NO EXISTE: Hacemos el flujo original (crear fila nueva)
            var rs = reCalculatePromo(id_f);

            if(id_f != 'undefined'){
                $('tr', 'table#productos tbody').each(function(){
                    var tr = $(this);
                    if($(this).attr('id_f') == id_f){
                        $('td.pu', tr).text(rs.precio);
                        $('td.pt', tr)
                            .attr('value', rs.precio * $('.editableCount', tr).val())
                            .text(rs.precio * $('.editableCount', tr).val());
                    }
                });
            }

            if($('td.secundary').css('display') == 'none'){
                $.colorbox.resize({width: '1200'});
                $('td.secundary').show();
            }

            drawProduct({
                id       : $('input[name=productosCant]').attr('params'),
                label    : $('input[name=productosCant]').val(),
                precio   : rs.precio,
                cantidad : $('input[name=cantidadP]').val(),
                idFamilia: $('input[name=productosCant]').attr('idfamilia')
            });

            window.added += 1;
            product_count += 1;
        }

        calculateTotal();
        resetForm();
    }

    var drawProduct = function(p){
        var trClass = 'detalles-dato-blue';
        var tdProd  = '<td params="'+p.id+'">'+p.label+'</td>';
        var tdCant  = '<td class="cant" style="width: 120px;" params="'+p.cantidad+'"><input type="number" style="width: 100px;" class="editableCount" value="'+p.cantidad+'" /></td>';
        var tdPU    = '<td class="pu">'+p.precio+'</td>';
        var tdPT    = '<td class="pt" value="'+p.precio * p.cantidad+'">'+p.precio * p.cantidad+'</td>';
        var tdSupr  = '<td class="eliminar"><div class="eliminar" title="Quitar"></div></td>';
        var tr;

        if($('tr', 'table#productos tbody').first().hasClass('par')){
            trClass += ' impar';
        }else if($('tr', 'table#productos tbody').first().hasClass('impar')){
            trClass += ' par';
        }else{
            trClass += ' impar';
        }

        tr = '<tr id="product_'+product_count+'" class="'+trClass+'" producto="'+p.label+'" id_p="'+p.id+'"  id_f="'+p.idFamilia+'"  cantidad="'+p.cantidad+'">'  + tdProd + tdCant + tdPU + tdPT + tdSupr + '</tr>';
        $('tbody', 'table#productos').prepend(tr);
    }

    var reCalculatePromo = function(id_f, edit = false, el = null){
        id_f = id_f == 'undefined' ? null : id_f;
        var id_p = null;
        var params = [];

        if(el != null){
            id_p = el.attr('id_p');
            params.push({
                ID              : id_p,
                ID_F            : id_f,
                CANTIDAD        : $('.editableCount', el).val(),
                idListaPrecio   : $('input[name=listaDePrecios]').attr('params')
            });
        }else{
            params.push({
                ID              : $('input[name=productosCant]').attr('params'),
                ID_F            : id_f,
                CANTIDAD        : $('input[name=cantidadP]').val(),
                idListaPrecio   : $('input[name=listaDePrecios]').attr('params')
            });
        }

        if(id_f != null){
            $('tr', 'table#productos tbody').each(function(){
                if(id_f == $(this).attr('id_f') && $(this).attr('id_p') != id_p){
                    params.push({
                        ID              : $(this).attr('id_p'),
                        ID_F            : $(this).attr('id_f'),
                        CANTIDAD        : $(this).attr('cantidad'),
                        idListaPrecio   : $('input[name=listaDePrecios]').attr('params')
                    });
                }
            });
        }

        if(params.length == 0){
            return null;
        }

        window.callService('traerPrecioProducto', params);
        return window.rsService;
    }

    var calculateTotal = function(){
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        });

        var total = 0;
        $('tr', 'table#productos tbody').each(function(){
            total += parseFloat($('td.pt', $(this)).attr('value'));
        });

        $('th.total', 'table#productos thead').text(formatter.format(total));
        $('th.total', 'table#products_added thead').text(formatter.format(total));
    }

    var resetForm = function(){
        $('input[name=productosCant]').val('');
        $('input[name=productosCant]').attr('params', '');
        $('input[name=cantidadP]').val('1');
        $('input[name=descuento]').val('');
        $('input[name=lotes]').val('');
        $('input[name=fechaDesde]').val('');
        $('input[name=lotes]').val('');
        $('input[name=productosCant]').focus();
    }

    var hacerAlta = function(){
        $('input#errorParcial', 'table.parcial').val('0');
        if($('input#errorParcial', 'table.parcial').val() == '0'){
            showSecundary();
        }
    }

    $('div.add-button.altaStock').click(function(){
        hacerAlta();
    });

    $('input[name=cantidadP]', '#modal.bajaStock').live('keypress', function(e){
        e.stopImmediatePropagation();
        var keyCode = e.which;
        if(keyCode == 13){
            hacerAlta();
        }
    });

    $('div.send-button.resumenAltaStock').click(function(e){
        e.stopImmediatePropagation();
        $('tbody', 'table#products_added').html('');
        var trResumen = '';
        $('tr',  "table#productos tbody").each(function(i,v){
            var trClass = (i%2 == 0) ? 'detalles-dato-blue par' : 'detalles-dato-blue impar';
            var tdProd  = '<td params="'+$(this).attr('id_p')+'">'+$(this).attr('producto')+'</td>';
            var tdCant2 = '<td class="cant" style="width: 120px;" params="'+$(this).attr('cantidad')+'">'+$(this).attr('cantidad')+'</td>';
            var tdPU    = '<td class="pu">'+$('td.pu', $(this)).text()+'</td>';
            var tdPT    = '<td class="pt" value="'+$('td.pt', $(this)).text()+'">'+$('td.pt', $(this)).text()+'</td>';
            trResumen  += '<tr id="'+$(this).attr('id')+'" class="'+trClass+'">' + tdProd + tdCant2 + tdPU + tdPT + '</tr>';
        });

        $('tbody', 'table#products_added').html(trResumen);

        $('input#error', 'div#modal.inicio').val('0');

        $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });

        if($('input#error', 'div#modal.inicio').val() == '0'){
            $('div#modal.inicio').hide();

            $('td.cabecera-detalle-blue.clientes').html($('input[name=clientes]').val());
            $('td.cabecera-detalle-blue.remitos').html($('input[name=remitos]').val());
            $('td.cabecera-detalle-blue.tipoEntrega').html($('select[name=tipoEntrega]').val());
            $('td.cabecera-detalle-blue.facturas').html($('input[name=facturas]').val());
            $('td.cabecera-detalle-blue.observaciones').html($('textarea[name=observaciones]').val());
            $('div#modal.resumen').show();
        }
    });

    $('input[name=cantidadP]', '#modal.bajaStock').live('keypress', function (e) {
        e.stopImmediatePropagation();
        var which = e.which;
        var keyCode = e.keyCode;
        if((which >= 48 && which <= 57) || keyCode == 8 || keyCode == 9 || keyCode == 37 || keyCode == 39 || keyCode == 46 || keyCode == 116 || keyCode == 13){
            return true;
        }else{
            return false;
        }
    });

    $('.editableCount').live('change', function(e){
        e.stopImmediatePropagation();

        if($(this).val() == $(this).closest('tr').attr('cantidad')){
            return false;
        }

        if($(this).val() <= 0){
            $.msgBox({
                title: "Alerta",
                content: 'La cantidad tiene que ser mayor a cero.'
            });
            return false;
        }

        var el   = $(this).closest('tr');
        var id_f = el.attr('id_f');

        if(id_f == 'undefined'){
            id_f = null;
        }

        var rs = reCalculatePromo(id_f, true, el);

        $('td.pu', el).html(rs.precio);
        var pt   = $(this).val() * ($('td.pu', el).html());
        el.attr('params', $(this).val());

        $(this).closest('td').attr('cantidad', $(this).val());
        $(this).closest('tr').attr('cantidad', $(this).val());
        $('td.pt', el).attr('value', pt).html(pt);

        if(id_f != 'undefined'){
            $('tr', 'table#productos tbody').each(function(){
                var tr = $(this);
                if($(this).attr('id_f') == id_f){
                    $('td.pu', tr).text(rs.precio);
                    $('td.pt', tr)
                        .attr('value', rs.precio * $('.editableCount', tr).val())
                        .text(rs.precio * $('.editableCount', tr).val());
                }
            });
        }

        calculateTotal();
    })

    $('div.eliminar', 'table#productos').live('click', function(e){
        e.stopImmediatePropagation();

        var el = $(this).closest('tr');
        var idProducto = el.attr('id');
        var id_f = el.attr('id_f');
        el.remove();
        window.added -= 1;

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

        if(id_f != 'undefined'){
            var rs = reCalculatePromo(id_f, true);
            $('tr', 'table#productos tbody').each(function(){
                var tr = $(this);
                if($(this).attr('id_f') == id_f){
                    $('td.pu', tr).text(rs.precio);
                    $('td.pt', tr)
                        .attr('value', rs.precio * $('.editableCount', tr).val())
                        .text(rs.precio * $('.editableCount', tr).val());
                }
            });
        }

        calculateTotal();
    });

    $('div.send-button.volver').click(function(e){
        e.stopImmediatePropagation();
        $('div#modal.resumen').hide();
        $('div#modal.inicio').show();
    });

    var enviado = false;
    $('div.hacerAltaStock').click(function(e){
        if(!enviado){
            enviado=true;
            e.stopImmediatePropagation();
            var productosSplited = '';
            $('tr', 'table#productos tbody').each(function(){
                productosSplited += $(this).attr('id_p') + '#' + $('td.cant input.editableCount', $(this)).val() + '#' + $(this).attr('id_f') + '||';
            });

            var params = {
                cantidadProductos : added,
                desdeAlta         : false,
                clientes          : $('input[name=clientes]').attr('params'),
                idLista           : $('input[name=listaDePrecios]').attr('params'),
                productos         : productosSplited,
                facturas          : $('input[name=facturas]').val(),
                remitos           : $('input[name=remitos]').val(),
                tipoEntrega       : $('select[name=tipoEntrega]').val(),
                descuento         : $('input[name=descuento]').val(),
                observaciones     : $('textarea[name=observaciones]').val(),
                tipo              : 'salidas'
            };
            $('div#modal').css('opacity', '0.4');
            $.post(
                'inc/process.php',
                {
                    <?php if(!empty($_GET['id'])) { ?>
                    idMovimientoAnt : <?=$_GET['id'];?>,
                    <?php } ?>
                    accion       : 'hacerAlta',
                    parametros   : params
                },
                function(data){
                    var response = JSON.parse(data);
                    enviado=false;
                    $.msgBox({ title: "Alerta", content: response.mensaje});

                    $('div#modal').css('opacity', '1');

                    if(!response.soyError){
                        $.colorbox.close();
                        window.loadFlix();
                    }
                }
            );
        }
    });
</script>
