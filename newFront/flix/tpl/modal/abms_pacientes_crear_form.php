<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Nombre</span></td>
        <td class="field-input"><input class="required" type="text" name="pacientes"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Observaciones</span></td>
        <td class="field-input"><textarea type="text" name="observaciones"/></td>
    </tr>
</table>

<script type="text/javascript">
    var abmValidar = function(){
        
        var error    = false;
        var paciente = $('input[name=pacientes]', 'table#abmCrear');
        
        if(paciente.val().trim() == '' || paciente.val().trim() == ''){
            paciente.addClass('error');
            error = true;
        }else{
            paciente.removeClass('error');
        }

        return error;
    }
    
    var abmCrear = function(){
        
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
                    desdeAlta         : false,
                    pacientes         : $('input[name=pacientes]', 'table#abmCrear').val(),
                    obrasSociales     : $('input[name=obrasSociales]', 'table#abmCrear').attr('params'),
                    nroAfiliado       : $('input[name=nroAfiliado]', 'table#abmCrear').val(),
                    pacienteTelefono  : $('input[name=pacienteTelefono]', 'table#abmCrear').val(),
                    observaciones     : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo              : 'pacientes'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
