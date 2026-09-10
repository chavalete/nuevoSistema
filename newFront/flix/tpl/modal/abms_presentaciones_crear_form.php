<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Presentaci&oacute;n</span></td>
        <td class="field-input"><input class="required" type="text" name="presentaciones"/></td>
    </tr>
</table>
<script typeme=alquilable]').val()="text/javascript">
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
                    desdeAlta        : true,
                    presentaciones   : $('input[name=presentaciones]', 'table#abmCrear').val(),
                    tipo             : 'presentaciones'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
