<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada mayor</span>
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
        <input type="hidden" name="tipo" value="mayor" />
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
                <td class="field-input"><input type="text" name="facturas" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Orden de cobro</span></td>
                <td class="field-input"><input type="text" name="ordenNro" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker"/></td>
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
        var ordeNro      = $('input[name=ordenNro]').val();        
        
        if(exportar == '1'){
            $.colorbox.close();
            window.open(window.urlServices + "exportador/exportadorFacturasVentas.php?accion=hacerAlta&clientes="+clientes+"&facturaNro="+facturaNro+"&ordenNro="+ordenNro+"&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&exportar="+exportar, "_blank");
        }        
    });
</script>
