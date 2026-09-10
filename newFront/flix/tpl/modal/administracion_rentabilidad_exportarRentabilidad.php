<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Rentabilidad</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportarStock" style="top: 30px; left: -300px; width: 320px; height: 85px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <input id="error" type="hidden" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Productos</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subfamilia</span></td>
                <td class="field-input"><input type="text" name="subfamilias" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Familia</span></td>
                <td class="field-input"><input type="text" name="familias" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Marca</span></td>
                <td class="field-input"><input type="text" name="marcas" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Tipo de Reporte</span></td>
                <td class="field-input">
                    <select name="tipoReporte">
                        <option value="sinAgrupar">Sin agrupar</option>
                        <option value="porCliente">Por Cliente</option>
                        <option value="porProducto">Por Producto</option>
                        <option value="porMarca">Por Marca</option>
                        <option value="porSubfamilia">Por Subfamilia</option>
                        <option value="porFamilia">Por Familia</option>
                        <option value="porRemito">Por Remito</option>
                    </select>
                </td>
            </tr>          
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker required"/></td>
            </tr>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarStock">Exportar stock</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        var fechaHasta     = $('input[name=fechaHasta]').val();
        var fechaDesde     = $('input[name=fechaDesde]').val();
        var cliente = $('input[name=clientes]').attr('params');
        var familia = $('input[name=familias]').attr('params');
        var productos = $('input[name=productos]').attr('params');
        var subfamilias = $('input[name=subfamilias]').attr('params');
        var marcas = $('input[name=marcas]').attr('params');
        var ordenNro = $('input[name=ordenNro]').attr('params');
        var tipoReporte  = $('select[name=tipoReporte]').val();
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open("http://172.234.31.142/limonLocal/exportador/exportadorRentabilidad.php?accion=hacerAlta&tipoReporte="+tipoReporte+"&fechaHasta="+fechaHasta+"&fechaDesde="+fechaDesde+"&subfamilias="+subfamilias+"&marcas="+marcas+"&idProducto="+productos+"&idFamilia="+familia+"&idCliente="+cliente, "_blank");
        }        
    });
</script>

