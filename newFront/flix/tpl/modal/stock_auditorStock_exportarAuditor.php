<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar auditor</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportarAuditor" style="top: 30px; left: -300px; width: 320px; height: 85px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <input id="error" type="hidden" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="productos"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker"/></td>
            </tr>                        
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker"/></td>
            </tr>
            <tr>
		<td class="field-name"><span class="field-name">Tipo de reporte</span></td>
		<td class="field-input">
		    <select name="tipoReporte">
			<option value="">Seleccionar</option>
			<option value="1">DT</option>
			<option value="2">Administraci&oacute;n</option>
		    </select>
		</td>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarAuditor">Exportar</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarAuditor').click(function(){
    
        var fechaDesde     = $('input[name=fechaDesde]').val();
        var fechaHasta     = $('input[name=fechaHasta]').val();
        var productos = $('input[name=productos]').attr('params');
        var tipoReporte  = $('select[name=tipoReporte]').val();
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open(window.urlServices + "exportador/exportarAuditor.php?accion=hacerAlta&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&idProducto="+productos+"&tipoReporte="+tipoReporte, "_blank");
        }        
    });
</script>

