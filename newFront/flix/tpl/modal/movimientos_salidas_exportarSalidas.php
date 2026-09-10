<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Salidas </span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportarSalidas" style="top: 30px; left: -300px; width: 320px; height: 65px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <input type="hidden" id="error" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Vendedor</span></td>
                <td class="field-input"><input type="text" name="vendedoresClientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker required"/></td>
            </tr>            
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportar">Exportar salida</div>
    </div>
</div>
<script type="text/javascript">
        $('div.exportar').click(function(){
        var exportar      = $('select[name=exportar]').val();        
        var clientes = $('input[name=clientes]').attr('params');
        var vendedores = $('input[name=vendedoresClientes]').attr('params');        
        var fechaDesde      = $('input[name=fechaDesde]').val();
        var fechaHasta      = $('input[name=fechaHasta]').val();
        var bonificacion      = $('input[name=bonificacion]').val();
        var relacion = $('input[name=relaciones]').attr('params');
        var cuenta = $('input[name=cuentas]').attr('params');
        
        $('input#error', 'div#modal.inicio').val('0');
        
        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });

        if($('input#error', 'div#modal.inicio').val() == '0'){
        
        
            $.colorbox.close();
            window.open(window.urlServices + "exportador/exportadorSalidas.php?accion=hacerAlta&clientes="+clientes+"&vendedores="+vendedores+"&bonificacion="+bonificacion+"&cuentaId="+cuenta+"&relacionId="+relacion+"&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&exportar="+exportar, "_blank");
	}
                
    });
</script>
