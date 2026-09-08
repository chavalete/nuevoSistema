<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Actualizar listas</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position" style='right: 30px; float: right;'>
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="actualizarListas" style="top: 30px; left: -300px; width: 320px; height: 365px;"></div>
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
			    </tr>                            
                            <tr>
                                <td colspan="3">
                                    <table class="parcial">
                                        <input id="errorParcial" type="hidden" value="0" />
                                        <tr>
                                            <td class="field-name"><span class="field-name">Producto</span></td>
                                            <td class="field-input"><input type="text" name="productos" id="productos" class="autocomplete parcial required"/></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Precio</span></td>
                                            <td class="field-input"><input type="text" name="precio" class="parcial required" /></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Costo</span></td>
                                            <td class="field-input"><input type="text" name="productoPcosto" class="parcial required"/></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Unidades Caja</span></td>
                                            <td class="field-input"><input type="text" name="unidadesPromo" class="parcial required" /></td>
                                            <td class="addButton"></td>
                                        </tr>
                                        <tr>
                                            <td class="field-name"><span class="field-name">Precio Caja</span></td>
                                            <td class="field-input"><input type="text" name="precioPromo" class="parcial required" /></td>
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
                        </table>
                    </form>
                </td>
                
                <td class="secundary" style="display: none; width: 720px; vertical-align: top;">
                    <div id="listas">
                        <table id="listas" align="left" style="width: 700px; margin-left: 10px;">
                            <thead>
                                <tr>
                                    <th class="detalles-titulo-dark">Producto.</th>
                                    <th class="detalles-titulo-dark">Precio.</th>
                                    <th class="detalles-titulo-dark">Costo.</th>
                                    <th class="detalles-titulo-dark">Unidades Promo.</th>
                                    <th class="detalles-titulo-dark">Precio Promo</th>
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
    <div class="modal_title"><span class="title">Actualizar lista - Resumen</span></div>
    
    <div class="modal_content">
        <div>
            <table style="width: 100%;">
            </table>
            
        </div>
        
        <div style="margin-top: 10px; width: 100%; height: 395px; overflow: auto;">
            <table id="products_added" style="width: 100%;">
                <thead>
                    <th class="detalles-titulo-dark">Producto.</th>
                    <th class="detalles-titulo-dark">Precio.</th>
                    <th class="detalles-titulo-dark">Costo.</th>
                    <th class="detalles-titulo-dark">Cant Promo.</th>
                    <th class="detalles-titulo-dark">Precio Promo.</th>
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
        var tdPrecio = '<td params="'+$('input[name=precio]').val()+'">'+$('input[name=precio]').val()+'</td>';
        var tdCosto = '<td params="'+$('input[name=productoPcosto]').val()+'">'+$('input[name=productoPcosto]').val()+'</td>';
        var tdCantPromo = '<td params="'+$('input[name=unidadesPromo]').val()+'">'+$('input[name=unidadesPromo]').val()+'</td>';
        var tdPrecioPromo = '<td params="'+$('input[name=precioPromo]').val()+'">'+$('input[name=precioPromo]').val()+'</td>';
        var tdSupr = '<td class="eliminar"><div class="eliminar" title="Quitar"></div></td>';
        var tr;
        var trResumen;

        if($('tr', 'table#listas tbody').first().hasClass('par')){
            trClass += ' impar';
        }else if($('tr', 'table#listas tbody').first().hasClass('impar')){
            trClass += ' par';
        }else{
            trClass += ' impar';
        }
        if($('tr', 'table#products_added tbody').first().hasClass('par')){
            trClassResumen += ' impar';
        }else if($('tr', 'table#listas tbody').first().hasClass('impar')){
            trClassResumen += ' par';
        }else{
            trClassResumen += ' impar';
        }
            
        tr = '<tr id="product_'+product_count+'" class="'+trClass+'">' +  tdProd +  tdPrecio + tdCosto + tdCantPromo + tdPrecioPromo + tdSupr + '</tr>';
        trResumen = '<tr id="product_'+product_count+'" class="'+trClassResumen+'">' + tdProd + tdPrecio + tdCosto + tdCantPromo + tdPrecioPromo +  '</tr>';
        $('tbody', 'table#listas').prepend(tr);
        $('tbody', 'table#products_added').prepend(trResumen);
        window.added += 1;
        product_count += 1;
        resetForm();
                  
    }
    var resetForm = function(){
        $('input[name=precio]').val('');
        $('input[name=precioPromo]').val('');
        $('input[name=unidadesPromo]').val('');
        $('input[name=productos]').val('');
        $('input[name=productos]').attr('params', '');
        $('input[name=listaDePrecios]').val('');
        $('input[name=listaDePrecios]').attr('params', '');
        $('input[name=merma]').val('');
        $('input[name=lotes]').val('');
        $('input[name=fechaDesde]').val('');
        $('input[name=productos]').focus();
        
        
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
    // Validar y agregar listas a la lista en el modal de Alta de stock
    $('div.add-button.altaStock').click(function(){
        hacerAlta();
    });
    
    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenAltaStock').click(function(){
        
            $('input#error', 'div#modal.inicio').val('0');
            
            $('input.required:not(.parcial)', 'div#modal.inicio').each(function(){
                window.validateInputs($(this));
            });
                        
            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('div#modal.inicio').hide();

                $('td.cabecera-detalle-blue.aumento').html($('input[name=aumento]').val());
                $('td.cabecera-detalle-blue.desc').html($('input[name=desc]').val());
                $('td.cabecera-detalle-blue.precio').html($('input[name=precio]').val());
                $('td.cabecera-detalle-blue.familias').html($('input[name=familias]').val());
                $('td.cabecera-detalle-blue.productos').html($('input[name=productos]').val());
                $('div#modal.resumen').show();
            }
        
    });    
    // valido que el input de cantidad solo permita numeros
    $('input[name=cantidad]').keypress(function (e) {
        var which = e.which;
        var keyCode = e.keyCode;
        // keyCode = 8 -> backspace || 9 -> tabulador 37 -> left || 39 -> right || 46 -> Suprimir || 116 -> F5
        // which 48 -> 57 son caracteres numericos
        if((which >= 46 && which <= 57) || keyCode == 8 || keyCode == 9 || keyCode == 46 || keyCode == 37 || keyCode == 39 || keyCode == 46 || keyCode == 116){ 
            return true;
        }else{
            return false;
        }
    });
    var cargarCosto = function(datos){
        var form = $('form.modal', 'div#modal');
        $('input[name=productoPcosto]', form).val(typeof(datos.productoPcosto) == 'undefined' ? '' : datos.productoPcosto);
        $('input[name=unidadesPromo]', form).val(typeof(datos.productoPcosto) == 'undefined' ? '' : datos.unidadesPromo);
        $('input[name=precioPromo]', form).val(typeof(datos.precioPromo) == 'undefined' ? '' : datos.precioPromo);
        $('input[name=precio]', form).val(typeof(datos.precio) == 'undefined' ? '' : datos.precio);
    }
    var vaciarCosto = function(){
        var form = $('form.modal', 'div#modal');
        $('input[name=productoPcosto]', form).val('');
        $('input[name=precio]', form).val('');
        $('input[name=precioPromo]', form).val('');
        $('input[name=unidadesPromo]', form).val('');
    }
    
    
    // Ajax para cargar la direccion del cliente dinamicamente
    $("#productos").blur(function() {
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
    $('div.eliminar', 'table#listas').live('click', function(){
        var idProducto = $(this).closest('tr').attr('id');
        window.added -= 1;
        
        $(this).closest('tr').remove();
        $('tr#'+idProducto , 'table#products_added').remove();
        
        $('tr', 'table#listas, table#products_added').each(function(index){
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
        $('input[name=precioPromo]').keypress(function(e){
        if(e.which == 13){
            $('div.add-button.altaStock', $(this).closest('td.primary')).click();
        }
    });
    // VOLVER
    $('div.send-button.volver').click(function(){
        $('div#modal.resumen').hide();
        $('div#modal.inicio').show();
    });  
    // REALIZAR ALTA
    $('div.hacerAltaStock').click(function(){
        var listasSplited = '';
        $('tr', 'table#listas tbody').each(function(){
            $('td', this).each(function(){
                if(typeof $(this).attr('params') != 'undefined' && $(this).attr('params').trim() != ''){
                    listasSplited += $(this).attr('params');
                    listasSplited += '#';
                }
            });
            listasSplited += '||';
        });
        var params = {
            cantidadProductos : added,
            desdeAlta         : false,
            aumento          : $('input[name=aumento]').val(),
            desc       : $('input[name=desc]').val(),
            precio       : $('input[name=precio]').val(),
            familias    : $('input[name=familias]').attr('params'),
            productos    : $('input[name=productos]').attr('params'),
            listas         : listasSplited,
            tipo              : 'actualizarListas'
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
