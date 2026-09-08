<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Nombre</span></td>
        <td class="field-input"><input class="required" type="text" name="vehiculos"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Volumen</span></td>
        <td class="field-input"><input type="text" class="required" name="volumen"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Observaciones</span></td>
        <td class="field-input"><textarea type="text" name="observaciones"/></td>
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
                    desdeAlta             : false,
                    vehiculos        : $('input[name=vehiculos]', 'table#abmCrear').val(),
                    vehiculoVolumen      : $('input[name=volumen]', 'table#abmCrear').val(),
                    observaciones         : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo                  : 'vehiculos'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
