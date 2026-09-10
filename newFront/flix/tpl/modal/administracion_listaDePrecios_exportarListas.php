<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Lista</span>
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
                <td class="field-name"><span class="field-name">Lista de precios</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="listaDePrecios"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button exportarStock">Exportar</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        
        var lista = $('input[name=listaDePrecios]').attr('params');
        var fecha = $('input[name=listaDePrecios]').attr('params');
        
        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });
        
        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open(window.urlServices + "exportador/exportadorListaDePrecios.php?accion=hacerAlta&fechaHasta="+fecha+"&idLista="+lista, "_blank");
        }        
    });
</script>

