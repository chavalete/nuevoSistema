<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Nombre</span></td>
        <td class="field-input"><input class="required" type="text" name="distribuidores"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Nro de Telefono</span></td>
        <td class="field-input"><input type="text" name="distribuidorTelefono"/></td>
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
                    distribuidores        : $('input[name=distribuidores]', 'table#abmCrear').val(),
                    distribuidorTelefono  : $('input[name=distribuidorTelefono]', 'table#abmCrear').val(),
                    distribuidorMail      : $('input[name=distribuidorMail]', 'table#abmCrear').val(),
                    observaciones         : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo                  : 'distribuidores'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
