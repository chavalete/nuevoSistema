<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
    <td colspan="3">
        <table class="parcial">
            <input id="errorParcial" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Herramienta</span></td>
                <td class="field-input"><input type="text"  class="required" name="herramienta"/></td>
            </tr>
        </table>
<script typeme="text/javascript">
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
                desdeAlta  : false,
                herramienta: $('input[name=herramienta]', 'table#abmCrear').val(),
                tipo       : 'usuariosHerramientas',
                
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>


