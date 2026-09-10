<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Obra social</span></td>
        <td class="field-input"><input class="required" type="text" name="obraSocial"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Observaciones</span></td>
        <td class="field-input"><textarea type="text" name="observaciones"/></td>
    </tr>
</table>

<script type="text/javascript">
    var abmValidar = function(){
        
        var error      = false;
        var obraSocial = $('input[name=obraSocial]', 'table#abmCrear');
        
        if(obraSocial.val().trim() == '' || obraSocial.val().trim() == ''){
            obraSocial.addClass('error');
            error = true;
        }else{
            obraSocial.removeClass('error');
        }
        
        return error;
    }
    
    var abmCrear = function(){
        
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
                    desdeAlta     : false,
                    obraSocial    : $('input[name=obraSocial]', 'table#abmCrear').val(),
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'obrasSociales'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>