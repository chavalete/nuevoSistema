<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Hoja de Ruta</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="altaHDR" style="top: 30px; left: -300px; width: 320px; height: 300px;"></div>
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
                            <td class="field-name"><span class="field-name">Remito</span></td>
                            <td class="field-input"><input type="text" name="remitos" /></td>
                            <td class="addButton"></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Distribuidor</span></td>
                            <td class="field-input"><input type="text" class="autocomplete required" name="distribuidores"/></td>
                        </tr>            
                        <tr>
                            <td class="field-name"><span class="field-name">Acompañante</span></td>
                            <td class="field-input"><input type="text" name="acompanante"/></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Vehiculo</span></td>
                            <td class="field-input"><input type="text" name="vehiculos" class="autocomplete required"/></td>
                        </tr>
                        <tr>
                            <td class="field-name"><span class="field-name">Carga</span></td>
                            <td class="field-input"><input type="text" name="carga"/></td>
                        </tr>
                        <tr>
			    <td class="field-name"><span class="field-name">Acción</span></td>
			    <td class="field-input">
			    <select name="operacion">
			    <option value="alta">Alta</option>
			</select>
			    </td>
			</tr>
			<tr>
			    <td class="field-name"><span class="field-name">Observaciones</span></td>
			    <td class="field-input"><textarea type="text" name="observaciones"/></td>
			</tr>
                    </table>
                </td>
                
                <td class="secundary" style="display: none; width: 360px; vertical-align: top;">
                    <div id="pedidos">
                        <table id="pedidos" align="left" style="width: 340px;">
                            <thead>
                                <tr>
                                    <th class="detalles-titulo">Remito</th>
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
        <div class="send-button resumenSalidaStock">Siguiente</div>
    </div>
</div>

<div id="modal" class="resumen" style="display: none">
    <div class="modal_title"><span class="title">Hoja de ruta - Resumen</span></div>
    
    <div class="modal_content">
        <div>
            <table style="width: 100%;">
                <tr>
                    <td class="cabecera-title">Distribuidor</td>
                    <td class="cabecera-detalle distribuidores"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Acompañante</td>
                    <td class="cabecera-detalle acompanante"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Vehiculo</td>
                    <td class="cabecera-detalle vehiculos"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Carga</td>
                    <td class="cabecera-detalle carga"></td>
                </tr>
                <tr>
                    <td class="cabecera-title">Observaciones</td>
                    <td class="cabecera-detalle observaciones"></td>
                </tr>            
            </table>
            
        </div>
        
        <div style="margin-top: 10px; width: 100%; height: 165px; overflow: auto;">
            <table id="products_added" style="width: 100%;">
                <thead>
                    <th class="detalles-titulo">Remito</th>
                    <th class="detalles-titulo">Descripcion</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <hr class="modal-divisor" />
        <div class="send-button volver">Volver</div>        
        <div class="send-button hacerSalidaStock">Procesar</div>        
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
    
    var counter = Number(0);
    window.added = Number(0);
    
    // MOSTRAR RESUMEN DE CONFIRMACION
    $('div.send-button.resumenSalidaStock').click(function(){
        
            // Reseteo los errores antes de verificar
            $('input#error', 'div#modal.inicio').val('0');
            
            $('input.required', 'div#modal.inicio').each(function(){
                window.validateInputs($(this));
            });
           
            if($('input#error', 'div#modal.inicio').val() == '0'){
                $('div#modal.inicio').hide();

                $('td.cabecera-detalle.distribuidores').html($('input[name=distribuidores]').val());
                $('td.cabecera-detalle.acompanante').html($('input[name=acompanante]').val());
                $('td.cabecera-detalle.vehiculos').html($('input[name=vehiculos]').val());
                $('td.cabecera-detalle.estados').html($('input[name=estados]').val());
                $('td.cabecera-detalle.carga').html($('input[name=carga]').val());
                $('td.cabecera-detalle.observaciones').html($('input[name=observaciones]').val());

                $.colorbox.resize({height: '575'});
                $('div#modal.resumen').show();
                
            }
        
    });
    
    // VOLVER
    $('div.send-button.volver').click(function(){
        $('div#modal.resumen').hide();
        $('div#modal.inicio').show();
        $.colorbox.resize({height: '530'});
    });     
    
    $('input[name=remitos]', 'div#modal').keypress(function(e){
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
            var tdCod = '<td class="codigo_inserted"></td>';
            var tdDes = '<td id="codigo_'+counter+'"></td>'
            var tdSup = '<td><div class="eliminar" title="Quitar"></div></td>';
            var tr;
            var trResumen;

            if($('tr', 'table#pedidos tbody').first().hasClass('par')){
                trClass += ' impar';
            }else if($('tr', 'table#pedidos tbody').first().hasClass('impar')){
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
            
            $('tbody', 'table#pedidos').prepend(tr);
            $('tbody', 'table#products_added').prepend(trResumen);

            $.post(
                'inc/process.php',
                {
                    accion: 'lectorCodigo',
                    tipo: 'remitos',
                    parametros: { codigo: $(this).val() }
                }, 
                function(data){
                    var td = 'td#codigo_'+counter;
                    var tr = $(td).closest('tr');
                    $('td.codigo_inserted', tr).html(data.codigo);                
                    $(td).html(data.descripcion);                    
                },
                'json'
            );
            $(this).val('');
        }
        
    });
    $('table#pedidos').delegate('div.eliminar', 'click', function(){
        var idProducto = $(this).closest('tr').attr('id');
        
        $(this).closest('tr').remove();
        $('tr#'+idProducto , 'table#products_added').remove();
        
        $('tr', 'table#pedidos, table#products_added').each(function(index){
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
        window.added -= 1;
    });
    // REALIZAR ALTA
    $('div.hacerSalidaStock').click(function(){
        var pedidosSplited = '';
        $('td.codigo_inserted', 'table#pedidos tbody').each(function(){
            pedidosSplited += $(this).html();
            pedidosSplited   += '||';
        });
        var params = {
            distribuidores     : $('input[name=distribuidores]').attr('params'),
            estados     : $('input[name=estados]').attr('params'),
            remitos     : pedidosSplited,
            operacion      : $('select[name=operacion]').val(),
            desdeAlta   : false,
            acompanante     : $('input[name=acompanante]').val(),
            carga     : $('input[name=carga]').val(),
            vehiculo     : $('input[name=vehiculos]').attr('params'),
            observaciones     : $('input[name=observaciones]').val(),
            tipo        : 'hdr'
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
