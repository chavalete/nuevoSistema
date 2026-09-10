<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Productos </span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportarProductos" style="top: 30px; left: -300px; width: 320px; height: 65px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <input type="hidden" id="error" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Actualizar Precio</span></td>
                <td class="field-input">
                    <select name="actualizarPrecio">
                        <option value="t">Si</option>
                        <option value="f">No</option>
                        <option value="todos">Todos</option>
                    </select>
                </td>
            </tr>          
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportar">Exportar salida</div>
    </div>
</div>
<script type="text/javascript">
        $('div.exportar').click(function(){
        var actualizarPrecio      = $('select[name=actualizarPrecio]').val();        
        var proveedores = $('input[name=proveedores]').attr('params');
        var vendedores = $('input[name=vendedores]').attr('params');        
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
            window.open(window.urlServices + "exportador/exportadorProductos.php?accion=hacerAlta&idProveedor="+proveedores+"&vendedores="+vendedores+"&bonificacion="+bonificacion+"&cuentaId="+cuenta+"&relacionId="+relacion+"&fechaDesde="+fechaDesde+"&fechaHasta="+fechaHasta+"&actualizarPrecio="+actualizarPrecio, "_blank");
	}
                
    });
</script>
