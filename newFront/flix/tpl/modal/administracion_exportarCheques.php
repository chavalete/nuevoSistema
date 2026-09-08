<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar stock</span>
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
                <td class="field-name"><span class="field-name">Imputacion</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="imputacionesDos"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Estado</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="estadosDos"/></td>
            </tr>
            <tr>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarStock">Exportar</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        var fecha     = $('input[name=fecha]').val();
        var imputacion = $('input[name=imputacionesDos]').attr('params');
        var estado = $('input[name=estadosDos]').attr('params');
        var tipoReporte  = $('select[name=tipoReporte]').val();
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open("http://172.104.5.168/newFront/exportador/exportadorCheques.php??accion=hacerAlta&tipoReporte="+tipoReporte+"&idImputacion="+imputacion+"&idEstado="+estado, "_blank");
        }        
    });
</script>

