<div id="modal" class="inicio bajaStock">
    <div class="modal_title">
        <span class="title">Editar Entrada</span>
        <div class='tooltip'>
            <div class="tooltip-position" style='right: 30px; float: right;'>
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="salidas" style="top: 30px; left: -300px; width: 320px; height: 365px;"></div>
            </div>
        </div>
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
                                            <td class="field-name"><span class="field-name">Producto</span></td>
                                            <td class="field-input"><input type="text" class="autocomplete parcial required" name="productos" loading="1" id="productos"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Cantidad</span></td>
                                            <td class="field-input"><input type="text" name="cantidad" id="cantidad" class="parcial" value="1"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Costo</span></td>
                                            <td class="field-input"><input type="text" name="productoPcosto" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Vencimiento</span></td>
                                            <td class="field-input"><input type="text" name="productoVencimiento" class="parcial datePicker" autocomplete="off"/></td>
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
                                    <th colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</th>
                                    <th colspan="1" class="total" style="text-align: left; padding-leftt: 10px; color: red"></th>
                                </tr>
                                <tr>
                                    <th class="detalles-titulo-dark">Producto</th>
                                    <th class="detalles-titulo-dark">Cantidad</th>
                                    <th class="detalles-titulo-dark">Costo U</th>
                                    <th class="detalles-titulo-dark">Costo T</th>
                                    <th class="detalles-titulo-dark">Vencimiento</th>
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
                        <th colspan="4" style="text-align: right; padding-right: 10px;">TOTAL</th>
                        <th colspan="1" class="total" style="text-align: left; padding-leftt: 10px; color: red"></th>
                    </tr>
                    <tr>
                        <th class="detalles-titulo-dark">Producto</th>
                        <th class="detalles-titulo-dark">Cantidad</th>
                        <th class="detalles-titulo-dark">Costo U</th>
                        <th class="detalles-titulo-dark">Costo T</th>
                        <th class="detalles-titulo-dark">Vencimiento</th>
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
                accion: 'traerDatosMovimientoEntradas',
                id: <?=!empty($_GET['id'])?$_GET['id']:0;?>,
                tipo: 'salidas'
            },
            function(response){
                response = JSON.parse(response);

                $("input[name=proveedores]")
                    .attr("params", response.proveedor.id)
                    .attr("idlistaprecio", response.proveedor.idListaPrecio)
                    .attr("title", response.proveedor.label)
                    .val(response.proveedor.label)
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

        var existProduct = false;
        $('tr', 'table#productos tbody').each(function(){
            if($('input[name=productos]').attr('params') == $(this).attr('id_p')){
                existProduct = true;
            }
        });


        var id_f = $('input[name=productos]').attr('idfamilia');

        var rs = reCalculatePromo(id_f);

        if(id_f != 'undefined' && rs != null){
            $('tr', 'table#productos tbody').each(function(){
                var tr = $(this);
                if($(this).attr('id_f') == id_f){
                    $('td.pu', tr).text(rs.costo);
                    $('td.pt', tr)
                        .attr('value', rs.costo * $('.editableCount', tr).val())
                        .text(rs.costo * $('.editableCount', tr).val());
                }
            });
        }


        if($('td.secundary').css('display') == 'none'){
            $.colorbox.resize({width: '1200'});
            $('td.secundary').show();
        }

        var inputVencimiento = $('input[name=productoVencimiento]').val();

        // Tomamos el costo directamente del campo parcial completado por el usuario
        var costoIngresado = parseFloat($('input[name=productoPcosto]').val()) || 0;

        drawProduct({
            id       : $('input[name=productos]').attr('params'),
            label    : $('input[name=productos]').val(),
            costo    : costoIngresado,
            cantidad : $('input[name=cantidad]').val(),
            idFamilia: $('input[name=productos]').attr('idfamilia'),
            vencimiento: inputVencimiento
        })

        window.added += 1;
        product_count += 1;

        calculateTotal();
        resetForm();
    }

    var drawProduct = function(p){
        /*
            p = {
                id: "int",
                label: "string",
                precio: "number",
                cantidad: "int",
                idFamilia: "string"    521/12/undefined
                vencimiento: "string" o fechaVencimiento: "string"
            }
        */
        var currentVencimiento = '';
        if (p.vencimiento) {
            currentVencimiento = p.vencimiento;
        } else if (p.fechaVencimiento) {
            currentVencimiento = p.fechaVencimiento;
        }

        var trClass = 'detalles-dato-blue';
        var tdProd  = '<td params="'+p.id+'">'+p.label+'</td>';
        var tdCant  = '<td class="cant" style="width: 120px;" params="'+p.cantidad+'"><input type="number" style="width: 100px;" class="editableCount" value="'+p.cantidad+'" /></td>';
        var tdPU    = '<td class="pu">'+p.costo+'</td>';
        var tdPT    = '<td class="pt" value="'+Math.round((p.costo * p.cantidad) * 100) / 100+'">'+Math.round((p.costo * p.cantidad) * 100) / 100+'</td>';
        var tdVenc  = '<td class="vencimiento-td" data-vencimiento="'+currentVencimiento+'">'+currentVencimiento+'</td>';
        var tr;

        if($('tr', 'table#productos tbody').first().hasClass('par')){
            trClass += ' impar';
        }else if($('tr', 'table#productos tbody').first().hasClass('impar')){
            trClass += ' par';
        }else{
            trClass += ' impar';
        }

        tr = '<tr id="product_'+product_count+'" class="'+trClass+'" producto="'+p.label+'" id_p="'+p.id+'"  id_f="'+p.idFamilia+'"  cantidad="'+p.cantidad+'">'  + tdProd + tdCant + tdPU + tdPT + tdVenc + '</tr>';
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
                idListaPrecio   : $('input[name=listaDePrecios').attr('params'),
                tipo    : 'productos'
            });
        }else{
            params.push({
                ID              : $('input[name=productos]').attr('params'),
                ID_F            : id_f,
                CANTIDAD        : $('input[name=cantidaP]').val(),
                idListaPrecio   : $('input[name=listaDePrecios').attr('params'),
                tipo    : 'productos'
            });
        }

        if(id_f != null){
            $('tr', 'table#productos tbody').each(function(){
                if(id_f == $(this).attr('id_f') && $(this).attr('id_p') != id_p){
                    params.push({
                        ID              : $(this).attr('id_p'),
                        ID_F            : $(this).attr('id_f'),
                        CANTIDAD        : $(this).attr('cantidad'),
                        idListaPrecio   : $('input[name=listaDePrecios').attr('params'),
                    });
                }
            });
        }

        if(params.lenght == 0){
            return null;
        }

        window.callService('traerDatos', params);
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
        $('input[name=productos]').val('');
        $('input[name=productos]').attr('params', '');
        $('input[name=cantidad]').val('');
        $('input[name=lotes]').val('');
        $('input[name=fechaDesde]').val('');
        $('input[name=estanterias]').val('');
        $('input[name=estanterias]').attr('params', '');
        $('input[name=productos]').focus();
        $('input[name=productoPcosto]').val('');
        $('input[name=productoVencimiento]').val('');
    }
    var hacerAlta = function(){
        $('input#errorParcial', 'table.parcial').val('0');

        // Validamos que los campos tengan valor y que la cantidad no sea negativa (< 0)
        var prodId = $('input[name=productos]').attr('params');
        var prodNom = $('input[name=productos]').val();
        var cantVal = parseFloat($('input[name=cantidad]').val());
        var costoVal = $('input[name=productoPcosto]').val();

        if(!prodId || prodNom.trim() === "" || isNaN(cantVal) || cantVal < 0 || costoVal.trim() === "") {
            $.msgBox({
                title: "Alerta",
                content: 'Por favor complete todos los datos requeridos correctamente. La cantidad debe ser mayor o igual a cero.'
            });
            return false;
        }

        if($('input#errorParcial', 'table.parcial').val() == '0'){
            showSecundary();
        }
    }
    // Validar y agregar productos a la lista en el modal de Alta de stock
    $('div.add-button.altaStock').click(function(){
        hacerAlta();
    });
    $('input[name=productoPcosto]', '#modal.bajaStock').live('keypress', function(e){
        e.stopImmediatePropagation();

        var keyCode = e.which;
        if(keyCode == 13){
            hacerAlta();
        }
    });

    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenAltaStock').click(function(e){
        e.stopImmediatePropagation();
        $('tbody', 'table#products_added').html('');
        var trResumen = '';
        $('tr',  "table#productos tbody").each(function(i,v){
            var currentVenc = $('td.vencimiento-td', $(this)).attr('data-vencimiento');
            var trClass = (i%2 == 0) ? 'detalles-dato-blue par' : 'detalles-dato-blue impar';
            var tdProd  = '<td params="'+$(this).attr('id_p')+'">'+$(this).attr('producto')+'</td>';
            var tdCant2 = '<td class="cant" style="width: 120px;" params="'+$(this).attr('cantidad')+'">'+$(this).attr('cantidad')+'</td>';
            var tdPU    = '<td class="pu">'+$('td.pu', $(this)).text()+'</td>';
            var tdPT    = '<td class="pt" value="'+$('td.pt', $(this)).text()+'">'+$('td.pt', $(this)).text()+'</td>';
            var tdVenc2 = '<td class="venc-resumen">'+(currentVenc ? currentVenc : '')+'</td>';
            trResumen  += '<tr id="'+$(this).attr('id')+'" class="'+trClass+'">' + tdProd + tdCant2 + tdPU + tdPT + tdVenc2 + '</tr>';
        });

        $('tbody', 'table#products_added').html(trResumen);

        $('input#error', 'div#modal.inicio').val('0');

        $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });

        if($('input#error', 'div#modal.inicio').val() == '0'){
            $('div#modal.inicio').hide();

            $('td.cabecera-detalle-blue.clientes').html($('input[name=clientes]').val());
            $('td.cabecera-detalle-blue.remitos').html($('input[name=remitos').val());
            $('td.cabecera-detalle-blue.tipoEntrega').html($('select[name=tipoEntrega]').val());
            $('td.cabecera-detalle-blue.facturas').html($('input[name=facturas').val());
            $('td.cabecera-detalle-blue.observaciones').html($('textarea[name=observaciones]').val());
            $('div#modal.resumen').show();
        }
    });
    // valido que el input de cantidad solo permita numeros
    $('input[name=cantidad]', '#modal.bajaStock').live('keypress', function (e) {
        e.stopImmediatePropagation();

        var which = e.which;
        var keyCode = e.keyCode;
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

    $('.editableCount').live('change', function(e){
        e.stopImmediatePropagation();

        if($(this).val() == $(this).closest('tr').attr('cantidad')){
            return false;
        }

        // Permite el valor 0 (la alerta salta únicamente con números negativos menores a cero)
        if($(this).val() < 0){
            $.msgBox({
                title: "Alerta",
                content: 'La cantidad tiene que ser mayor o igual a cero.'
            });
            return false;
        }

        var el   = $(this).closest('tr');
        var id_f = el.attr('id_f');

        if(id_f == 'undefined'){
            id_f = null;
        }

        var rs = reCalculatePromo(id_f, true, el);

        $('td.pu', el).html(rs.costo);
        var pt   = $(this).val() * ($('td.pu', el).html());
        el.attr('params', $(this).val());

        $(this).closest('td').attr('cantidad', $(this).val());
        $(this).closest('tr').attr('cantidad', $(this).val());
        $('td.pt', el).attr('value', pt).html(pt);

        if(id_f != 'undefined'){
            $('tr', 'table#productos tbody').each(function(){
                var tr = $(this);
                if($(this).attr('id_f') == id_f){
                    $('td.pu', tr).text(rs.costo);
                    $('td.pt', tr)
                        .attr('value', rs.costo * $('.editableCount', tr).val())
                        .text(rs.costo * $('.editableCount', tr).val());
                }
            });
        }

        calculateTotal();
    })

    // VOLVER
    $('div.send-button.volver').click(function(e){
        e.stopImmediatePropagation();
        $('div#modal.resumen').hide();
        $('div#modal.inicio').show();
    });
    // REALIZAR ALTA
    var enviado = false;
    $('div.hacerAltaStock').click(function(e){
    if(!enviado){
       enviado=true;
        e.stopImmediatePropagation();
        var productosSplited = '';
        $('tr', 'table#productos tbody').each(function(){
            var vencimientoVal = $('td.vencimiento-td', $(this)).attr('data-vencimiento');
            productosSplited += $(this).attr('id_p') + '#' + $('td.cant input.editableCount', $(this)).val() + '#' + $('td.pu', $(this)).text() + '#' + (vencimientoVal ? vencimientoVal : '') + '||';
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
