<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Imprimir Boletas</span>
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
                <td class="field-name"><span class="field-name">Fecha</span></td>
                <td class="field-input"><input type="text" name="fecha" class="datePicker"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Desde</span></td>
                <td class="field-input"><input type="text"  name="desde"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Hasta</span></td>
                <td class="field-input"><input type="text"  name="hasta"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarStock">Imprimir</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        var desde     = $('input[name=desde]').val();
        var hasta     = $('input[name=hasta]').val();
        var proveedor = $('input[name=proveedor]').attr('params');
        var fecha     = $('input[name=fecha]').val();
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open(window.urlServices + "generador_pdf/generadorFacturaLimonRangos.php?accion=hacerAlta&desde="+desde+"&fecha="+fecha+"&hasta="+hasta, "_blank");
        }        
    });
</script>

