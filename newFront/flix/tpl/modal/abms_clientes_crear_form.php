<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td>
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Clientes</span></td>
                    <td class="field-input"><input class="required" type="text" name="clientes"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Nro. CUIT</span></td>
                    <td class="field-input"><input class="required" type="text" name="cuit"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Posicion frente al IVA</span></td>
                    <td class="field-input"><input class="autocomplete" type="text" name="condicionIva" /></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Direccion</span></td>
                    <td class="field-input"><input type="text" name="direccion" /></td>
                    <td class="addButton"></td>
                </tr> 
                 <tr>
                     <td class="field-name"><span class="field-name">Telefono</span></td>
                     <td class="field-input"><input type="text" name="telefono" /></td>
                     <td class="addButton"></td>
                 </tr>
                 <tr>
                     <td class="field-name"><span class="field-name">Localidad</span></td>
                     <td class="field-input"><input type="text" class="autocomplete" name="localidades" /></td>
                     <td class="addButton"></td>
                 </tr>
                 <tr>
                     <td class="field-name"><span class="field-name">Horario Entrega</span></td>
                     <td class="field-input"><input type="text" name="horarioEntrega" /></td>
                     <td class="addButton"></td>
                 </tr>
                 <tr>
                     <td class="field-name"><span class="field-name">Observaciones</span></td>
                     <td class="field-input"><textarea type="text" name="observaciones"></textarea></td>
                 </tr>                  
            </table>
        </td>        
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
                    categoria     : $('input[name=categorias]', 'table#abmCrear').attr('params'),
                    cliente       : $('input[name=clientes]', 'table#abmCrear').val(),
                    direccion       : $('input[name=direccion]', 'table#abmCrear').val(),
                    calles       : $('input[name=calles]', 'table#abmCrear').val(),
                    alturas       : $('input[name=alturas]', 'table#abmCrear').val(),
                    piso       : $('input[name=piso]', 'table#abmCrear').val(),
                    depto       : $('input[name=depto]', 'table#abmCrear').val(),
                    vendedores     : $('input[name=vendedoresClientes]', 'table#abmCrear').attr('params'),
                    localidades     : $('input[name=localidades]', 'table#abmCrear').attr('params'),
                    listaId     : $('input[name=listaDePrecios]', 'table#abmCrear').attr('params'),
                    codigoPostal       : $('input[name=codigoPostal]', 'table#abmCrear').val(),
                    horarioEntrega       : $('input[name=horarioEntrega]', 'table#abmCrear').val(),
                    telefono      : $('input[name=telefono]', 'table#abmCrear').val(),
                    cuit          : $('input[name=cuit]', 'table#abmCrear').val(),
                    condicionIva     : $('input[name=condicionIva]', 'table#abmCrear').attr('params'),
                    formaPago      : $('input[name=formaPago]', 'table#abmCrear').val(),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    respInsc      : $('select[name=iva]').val(),
                    tipo          : 'clientes'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
