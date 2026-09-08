<table id="abmCrear">
    <input id="error" type="hidden" value="0" />
    <tr>
        <td class="field-name"><span class="field-name">Producto</span></td>
        <td class="field-input"><input class="required" type="text" name="productos"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Presentaci&oacute;n</span></td>
        <td class="field-input"><input class="required" id="presentacion" type="text" name="presentacion" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Unidades</span></td>
        <td class="field-input"><input class="required" type="text" name="unidades" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Codigo Referencia</span></td>
        <td class="field-input"><input class="required" type="text" name="codigoReferencia"/></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Cod.Barras</span></td>
        <td class="field-input"><input type="text" name="codigoBarras" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Marca</span></td>
        <td class="field-input"><input class="autocomplete required" type="text" name="marcas" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Producto Costo</span></td>
        <td class="field-input"><input class="required" type="text" name="productoPcosto" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Precio Unitario</span></td>
        <td class="field-input"><input class="required" type="text" name="productoPrecio" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Precio x Caja</span></td>
        <td class="field-input"><input type="text" name="productoPrecioXCaja" /></td>
    </tr>
    <tr>
        <td class="field-name"><span class="field-name">Stock Alerta</span></td>
        <td class="field-input"><input class="required" type="text" name="stockAlerta" /></td>
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
    var cargarReferencia = function(datos){
        var form = $('div#modal.inicio');
        $('input[name=codigoReferencia]', form).val(typeof(datos.codigoReferencia) == 'undefined' ? '' : datos.codigoReferencia);
    }
    var vaciarCosto = function(){
        var form = $('form.modal', 'div#modal');
        $('input[name=codigoReferencia]', form).val('');
    }
    
    
    // Ajax para cargar la direccion del cliente dinamicamente
    $("#presentacion").blur(function() {
        if($(this).val() != 1){
            {
                $.post(
                    'inc/process.php',
                    {   
                        accion  : 'traerDatos',
                        id      : $('input[name=productos]').attr('params'),
                        tipo    : 'productosReferencia'
                    }, 
                    function(data){
                        if(data.soyError == false){
                            cargarReferencia(data);
                        }else{
                            $.msgBox({ title: "Error", content: 'No se pudo cargar el costo'});
                        }
                    },
                    'json'
                );   
            }
        }else{
            vaciarCosto();
        }
    });    
    
    var abmCrear = function(){
        
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
		    productoGtin : $('input[name=productoGtin]', 'table#abmCrear').val(),
                    desdeAlta        : false,
                    marcas           : $('input[name=marcas]', 'table#abmCrear').attr('params'),
                    familias           : $('input[name=familias]', 'table#abmCrear').attr('params'),
                    presentacion     : $('input[name=presentacion]', 'table#abmCrear').val(),
                    unidades               : $('input[name=unidades]', 'table#abmCrear').val(),
                    productoPrecio               : $('input[name=productoPrecio]', 'table#abmCrear').val(),
                    productoPrecioXCaja               : $('input[name=productoPrecioXCaja]', 'table#abmCrear').val(),
                    productoPcosto               : $('input[name=productoPcosto]', 'table#abmCrear').val(),
                    productoPcostoDolar               : $('input[name=productoPcostoDolar]', 'table#abmCrear').val(),
                    stockAlerta               : $('input[name=stockAlerta]', 'table#abmCrear').val(),
                    productoGtin               : $('input[name=codigoBarras]', 'table#abmCrear').val(),
                    codigoReferencia        : $('input[name=codigoReferencia]', 'table#abmCrear').val(),
                    productos        : $('input[name=productos]', 'table#abmCrear').val(),
                    tipo             : 'productos'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
    
</script>
