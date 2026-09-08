<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Producto Local</span></td>
        <td class="field-input"><input class="autocomplete required" type="text" name="productos"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Cod. Barras Reparto</span></td>
        <td class="field-input"><input class="required" type="text" name="gtin"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Unidades</span></td>
        <td class="field-input"><input class="required" type="text" name="unidades"/></td>
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
                    productos           : $('input[name=productos]', 'table#abmCrear').attr('params'),
                    presentaciones           : $('input[name=presentaciones]', 'table#abmCrear').val(),
                    unidades     : $('input[name=unidades]', 'table#abmCrear').val(),
                    gtin     : $('input[name=gtin]', 'table#abmCrear').val(),
                    unidadVenta       : $('select[name=unidadVenta]', 'table#abmCrear').val(),
                    unidadDistribucion       : $('select[name=unidadDistribucion]', 'table#abmCrear').val(),
                    tipo             : 'productosPresentaciones'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
