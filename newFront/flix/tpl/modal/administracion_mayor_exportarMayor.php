<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Mayor</span>
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
                <td class="field-name"><span class="field-name">Cuenta</span></td>
                <td class="field-input"><input type="text" name="relaciones" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Nro Factura</span></td>
                <td class="field-input"><input type="text" name="facturas" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Orden de cobro</span></td>
                <td class="field-input"><input type="text" name="ordenNro" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker"/></td>
            </tr>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarStock">Exportar</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        var fechaHasta     = $('input[name=fechaHasta]').val();
        var fechaDesde     = $('input[name=fechaDesde]').val();
        var cliente = $('input[name=clientes]').attr('params');
        var relacion = $('input[name=relaciones]').attr('params');
        var facturas = $('input[name=facturas]').attr('params');
        var ordenNro = $('input[name=ordenNro]').attr('params');
        var tipoReporte  = $('select[name=tipoReporte]').val();
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open("http://172.104.5.168/newFront/exportador/exportadorMayor.php?accion=hacerAlta&tipoReporte="+tipoReporte+"&fechaHasta="+fechaHasta+"&fechaDesde="+fechaDesde+"&relacionId="+relacion+"&ordenNro="+ordenNro+"&facturas="+facturas+"&idCliente="+cliente, "_blank");
        }        
    });
</script>

