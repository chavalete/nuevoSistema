<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de Promociones</span>
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="crear" style="top: 30px; left: -300px; width: 320px; height: 90px;"></div>
            </div>
        </div>
    </div>
    <div class="modal_content">
        <table id="abmCrear">
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Promocion Nombre</span></td>
                <td class="field-input"><input class="required" type="text" name="promocionNombre" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subfamilia</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="subfamilias" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="productos" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Cantidad de Productos</span></td>
                <td class="field-input"><input class="required" type="text" name="cantidad" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Precio Venta</span></td>
                <td class="field-input"><input class="required" type="text" name="precio_venta" readonly style="background-color: #eee;" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Precio</span></td>
                <td class="field-input"><input class="required" type="text" name="precio" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Porcentaje descuento</span></td>
                <td class="field-input"><input class="required" type="text" name="porcentaje" placeholder="Ej: 15" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha Inicio</span></td>
                <td class="field-input"><input type="text" name="fechaInicio" class="datePicker required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha Vencimiento</span></td>
                <td class="field-input"><input type="text" name="fechaVencimiento" class="datePicker required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Sucursal</span></td>
                <td class="field-input">
                    <select class="required" name="idSucursal" style="width: 100%; padding: 4px;">
                        <option value="1">TODAS</option>
                        <option value="2">Moreno</option>
                        <option value="3">Rosas</option>
                        <option value="4">Limoneta</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Observaciones</span></td>
                <td class="field-input"><textarea type="text" name="observaciones" /></td>
            </tr>
        </table>
        <hr class="modal-divisor" />

        <div class="send-button abmCrear">Crear</div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        // 1. Función para buscar el precio vía AJAX
        function buscarPrecio() {
            var subfamiliaId = $('input[name=subfamilias]').attr('params') || '';
            var productoId = $('input[name=productos]').attr('params') || '';

            if (subfamiliaId === '' && productoId === '') {
                $('input[name=precio_venta]').val('');
                $('input[name=precio]').val('');
                $('input[name=porcentaje]').val('');
                return;
            }

            $.post('inc/process.php', {
                accion: 'buscarPrecioVenta',
                parametros: {
                    subfamiliaId: subfamiliaId,
                    productoId: productoId
                }
            }, function(response) {
                if(response && response.precio) {
                    $('input[name=precio_venta]').val(response.precio);

                    // Si el usuario ya había metido un porcentaje antes de elegir el producto, calculamos precio.
                    // Si metió un precio, calculamos porcentaje.
                    if ($('input[name=porcentaje]').val() !== '') {
                        calcularPrecioDesdePorcentaje();
                    } else {
                        calcularDescuento();
                    }
                }
            }, 'json');
        }

        // 2. Evento nativo de jQuery UI Autocomplete
        $('input[name=subfamilias], input[name=productos]').on('autocompleteselect', function(event, ui) {
            $(this).attr('params', ui.item.id);
            setTimeout(buscarPrecio, 10);
        });

        // Control de limpieza manual
        $('input[name=subfamilias], input[name=productos]').on('input', function() {
            if ($(this).val().trim() === '') {
                $(this).removeAttr('params');
                buscarPrecio();
            }
        });

        // 3. Función: Escribe Precio -> Calcula Porcentaje
        function calcularDescuento() {
            var precioVenta = parseFloat($('input[name=precio_venta]').val());
            var precioFinal = parseFloat($('input[name=precio]').val());

            if (!isNaN(precioVenta) && !isNaN(precioFinal) && precioVenta > 0) {
                var descuento = 100 - ((precioFinal / precioVenta) * 100);
                if (descuento < 0) descuento = 0;
                if (descuento > 100) descuento = 100;

                // Ponemos solo el número para que no joda la edición manual posterior
                $('input[name=porcentaje]').val(descuento.toFixed(2));
            } else if (isNaN(precioFinal)) {
                $('input[name=porcentaje]').val('');
            }
        }

        // 4. Función: Escribe Porcentaje -> Calcula Precio
        function calcularPrecioDesdePorcentaje() {
            var precioVenta = parseFloat($('input[name=precio_venta]').val());
            var porcentaje = parseFloat($('input[name=porcentaje]').val());

            if (!isNaN(precioVenta) && !isNaN(porcentaje) && precioVenta > 0) {
                if (porcentaje < 0) porcentaje = 0;
                if (porcentaje > 100) porcentaje = 100;

                var precioFinal = precioVenta - ((porcentaje * precioVenta) / 100);
                $('input[name=precio]').val(precioFinal.toFixed(2));
            } else if (isNaN(porcentaje)) {
                $('input[name=precio]').val('');
            }
        }

        // Listeners cruzados para el cálculo dinámico
        $('input[name=precio]').on('input', function() {
            calcularDescuento();
        });

        $('input[name=porcentaje]').on('input', function() {
            calcularPrecioDesdePorcentaje();
        });

    });

    // --- Lógica original de envío de formulario ---
    $('div.abmCrear').click(function(){
        window.rsAbm = '';
        if(!abmValidar()){
            $.ajaxSetup({async: false});
            abmCrear();
            $.ajaxSetup({async: true});
            $.msgBox({ title: "Alerta", content: window.rsAbm.mensaje});
            if(!window.rsAbm.soyError){
                $.colorbox.close();
                window.loadFlix();
            }
        }
    });

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
                    promocionNombre    : $('input[name=promocionNombre]', 'table#abmCrear').val(),
                    subfamiliaId       : $('input[name=subfamilias]').attr('params'),
                    productoId         : $('input[name=productos]').attr('params'),
                    promocionCantidad  : $('input[name=cantidad]', 'table#abmCrear').val(),
                    precioVenta        : $('input[name=precio_venta]', 'table#abmCrear').val(),
                    promocionPrecio    : $('input[name=precio]', 'table#abmCrear').val(),
                    porcentajeDescuento: $('input[name=porcentaje]', 'table#abmCrear').val(), // Ya va limpio sin %
                    fechaVencimiento   : $('input[name=fechaVencimiento]', 'table#abmCrear').val(),
                    fechaInicio   : $('input[name=fechaInicio]', 'table#abmCrear').val(),
                    idSucursal   : $('select[name=idSucursal]', 'table#abmCrear').val(),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'promociones'
                }
            },
            function(data){
                window.rsAbm = data;
            },
            'json'
        );
    }
</script>
