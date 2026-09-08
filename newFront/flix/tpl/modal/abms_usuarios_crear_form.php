<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
    <td colspan="3">
        <table class="parcial">
            <input id="errorParcial" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Nombre completo</span></td>
                <td class="field-input"><input type="text" class="required" name="nombreCompleto"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Nombre de usuario</span></td>
                <td class="field-input"><input type="text" class="required" name="nombreUsuario"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Contraseña</span></td>
                <td class="field-input"><input type="password" name="contrasena" class="required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Sucursal</span></td>
                <td class="field-input"><input type="text" class="autocomplete required" name="sucursales"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subseccion inicio</span></td>
                <td class="field-input"><input type="text" class="autocomplete required" name="modelos"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Usuario administrador</span></td>
                <td class="field-input">
                <select name="usuarioAdmin">
                <option value="f">No</option>
                <option value="t">Si</option>
                </select>
            </td>
            </tr>
</table>
<script typeme=usuarioAdmin]').val()="text/javascript">
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
                desdeAlta           : false,
                usuario_nombre_completo    : $('input[name=nombreCompleto]', 'table#abmCrear').val(),
                usuario_nombre    : $('input[name=nombreUsuario]', 'table#abmCrear').val(),
                usuario_password           : $('input[name=contrasena]', 'table#abmCrear').val(),
                usuario_administrador      : $('select[name=usuarioAdmin]', 'table#abmCrear').val(),
                usuario_bas : $('input[name=usuarioBas]', 'table#abmCrear').val(),
                subseccion : $('input[name=modelos]', 'table#abmCrear').val(),
                tipo                : 'usuarios'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
