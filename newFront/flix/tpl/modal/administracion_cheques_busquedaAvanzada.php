<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de Cheques</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 50px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->          
    </div>
    <div class="modal_content">
        <input type="hidden" name="tipo" value="cheques" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Banco</span></td>
                <td class="field-input"><input type="text" name="bancos" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Nro Cheque</span></td>
                <td class="field-input"><input type="text" name="cheques" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Estado</span></td>
                <td class="field-input"><input type="text" name="estados" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Imputado</span></td>
                <td class="field-input"><input type="text" name="imputaciones" class="autocomplete"/></td>
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
                <td class="field-name"><span class="field-name">Tipo de cheque</span></td>
                <td class="field-input">
                    <select name="formaId">
                        <option value="3"> Cheque </option>
                        <option value="6"> Echeq </option>
                    </select>
                </td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.busquedaAvanzada').click(function(){
        var exportar      = $('select[name=exportar]').val();        
        var clientes = $('input[name=clientes]').attr('params');        
        var fechaDesde      = $('input[name=fechaDesde]').val();
        var fechaHasta      = $('input[name=fechaHasta]').val();
        var facturaNro      = $('input[name=facturas]').val();
        var lote      = $('input[name=lotes]').val();        
        
        if(exportar == '1'){
            $.colorbox.close();
            window.open(window.urlServices + "exportador/exportadorFacturasVentas.php?accion=hacerAlta&clientes="+clientes+"&facturaNro="+facturaNro+"&lote="+lote+"&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&exportar="+exportar, "_blank");
        }        
    });
</script>
