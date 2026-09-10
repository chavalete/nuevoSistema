<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Reproceso de movimientos</span>
    </div>
    <div class="modal_content">
        <input type="hidden" id="error" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Nro. de Referencia</span></td>
                <td class="field-input"><input type="text" name="movimientos" class="required" name="movimientos"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Tipo Movimiento</span></td>
                <td class="field-input">
                    <select name="tipoMovimientoReproceso">
                        <option value="recepciones">Recepcion</option>
                        <option value="distribucion">Distribucion</option>
                    </select>
                </td>
            </tr>            
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button reprocesar">Reprocesar</div>
    </div>
</div>

<script type="text/javascript">
    $('div.send-button.reprocesar').click(function(){
        $('input#error', 'div#modal.inicio').val(0);
        
        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        }); 
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            var parametros = {
                movimientos      : $('input[name=movimientos]').val(),
                tipoMovimientoId : $('select[name=tipoMovimientoReproceso]').val(),
                tipo             : window.parametros.tipo
            }
            window.callService('hacerAlta', parametros);
            window.loadFlix();
        }
    });
</script>
