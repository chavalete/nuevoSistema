<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportador de movimientos SAP</span>
        <!-- Comienza la Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportadorMovimientosSAP" style="top: 30px; left: -300px; width: 320px; height: 50px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->         
    </div>
    <div class="modal_content">
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Nro. Referencia</span></td>
                <td class="field-input"><input type="text" class="" name="nro_referencia" /></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarSAP">Exportar</div>
    </div>
</div>

<script type="text/javascript">
    $('div.send-button.exportarSAP').click(function(){
        var referencia = $('input[name=nro_referencia]').val();

        $.colorbox.close();
        window.open(window.urlServices + "exportador/exportadorSAP.php?accion=exportarMovimientosSAP&referencia="+ referencia, "_blank");
    });
</script>