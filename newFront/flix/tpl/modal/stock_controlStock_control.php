<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de stock</span>
    </div>
    <div class="modal_content">
        <input id="error" type="hidden" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Estanteria</span></td>
                <td class="field-input"><input type="text" class="autocomplete" name="estanterias" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input type="text" class="autocomplete" name="productos" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Lote</span></td>
                <td class="field-input"><input type="text" name="lotes" /></td>
            </tr>

        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button controlar">Controlar</div>
    </div>
</div>

<script type="text/javascript">
    $('div.send-button.controlar').click(function(){
        $('input#error', 'div#modal.inicio').val('0');
        
        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });   
        
        if($('input#error', 'div#modal.inicio').val() == 0){
            var parametros = {
                desdeAlta   : false,
                estanterias : $('input[name=estanterias]').attr('params'),
                productos : $('input[name=productos]').attr('params'),
                lotes : $('input[name=lotes]').val(),
                tipo        : window.parametros.tipo
            }
            window.callService('hacerAlta', parametros);
        }
        
        if(window.rsService.soyError == 1){
            $.msgBox({ title: "Error", content: window.rsService.mensaje, type: 'error'});
        }else{
            $.colorbox.close();
            window.loadFlix();
        }
    });
</script>