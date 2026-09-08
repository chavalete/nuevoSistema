<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Proveedor</span></td>
        <td class="field-input"><input class="required" type="text" name="proveedores"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Dirección</span></td>
        <td class="field-input"><input type="text" name="direccion"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Nro. CUIT</span></td>
        <td class="field-input"><input type="text" name="proveCuit"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Contacto</span></td>
        <td class="field-input"><input type="text" name="contacto"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Nro. Telefono</span></td>
        <td class="field-input"><input type="text" name="proveTelefono"/></td>
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
                    desdeAlta     : false,
                    direccion     : $('input[name=direccion]', 'table#abmCrear').val(),
                    proveCuit     : $('input[name=proveCuit]', 'table#abmCrear').val(),
                    proveContacto     : $('input[name=contacto]', 'table#abmCrear').val(),
                    proveTelefono : $('input[name=proveTelefono]', 'table#abmCrear').val(),
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    proveedores   : $('input[name=proveedores]', 'table#abmCrear').val(),
                    gln     : $('input[name=gln]', 'table#abmCrear').val(),
                    tipo          : 'proveedores'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
