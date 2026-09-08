<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Estantería</span></td>
        <td class="field-input"><input class="required" type="text" name="estanterias"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Almacén</span></td>
        <td class="field-input"><input class="autocomplete required" type="text" name="almacenes" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Observaciones</span></td>
        <td class="field-input"><textarea type="text" name="observaciones" /></td>
    </tr>
</table>

<script type="text/javascript">
    var abmValidar = function(){
        $('input#error', 'div#modal.inicio').val('0');
        $('input.required', 'table#abmCrear').each(function(){
            window.validateInputs($(this));
        });     
        if($('input#error', 'div#modal.inicio').val() == '0'){
            return false;
        }else{
            return true;
        }
    }
    
    var abmCrear = function(){
        
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
                    almacenes     : $('input[name=almacenes]', 'table#abmCrear').attr('params'),
                    desdeAlta     : false,
                    estanterias   : $('input[name=estanterias]', 'table#abmCrear').val(),
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'estanterias'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
