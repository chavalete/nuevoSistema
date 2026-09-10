<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Reimprimir codigo de trazabilidad</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="reimprimirCodigoTrazabilidad" style="top: 30px; left: -300px; width: 320px; height: 40px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->          
    </div>
    <div class="modal_content">
        <table id="busquedaPorCodigo">
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Codigo de trazabilidad</span></td>
                <td class="field-input"><input type="text" name="codigo" class="required"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button modal-buscar-codigo">Buscar</div>
    </div>
</div>

<script type="text/javascript">
    $('div.send-button.modal-buscar-codigo').click(function(){
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'table#busquedaPorCodigo').each(function(){
            window.validateInputs($(this));
        }); 
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            window.flixResetValues();

            window.parametros.codigoTrazabilidad = $('input[name=codigo]').val();
            window.parametros.tipo= 'busquedaPorCodigo';

            window.loadFlix();
        }
    });
</script>
