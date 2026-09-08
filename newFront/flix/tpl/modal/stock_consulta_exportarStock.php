<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Exportar Stock</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="exportarStock" style="top: 30px; left: -300px; width: 320px; height: 85px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->
    </div>
    <div class="modal_content">
        <input id="error" type="hidden" value="0" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Productos</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subfamilia</span></td>
                <td class="field-input"><input type="text" name="subfamilias" class="autocomplete"/></td>
	    </tr>
	<tr>
                <td class="field-name"><span class="field-name">Familia</span></td>
                <td class="field-input"><input type="text" name="familias" class="autocomplete"/></td>
                <td class="addButton"></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Marca</span></td>
                <td class="field-input"><input type="text" name="marcas" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Ordenado Por:</span></td>
                <td class="field-input">
                    <select name="ordenadoPor">
                        <option value="marca_nombre,subfamilia_nombre">Marca</option>
                        <option value="subfamilia_nombre,marca_nombre">Subfamilia</option>
                    </select>
                </td>
            </tr>
	    </tr>
        </table>
        <hr class="modal-divisor" />

        <div class="send-button exportarStock">Exportar stock</div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-button.exportarStock').click(function(){
        var fechaHasta     = $('input[name=fechaHasta]').val();
        var fechaDesde     = $('input[name=fechaDesde]').val();
        var cliente = $('input[name=clientes]').attr('params');
        var familia = $('input[name=familias]').attr('params');
        var productos = $('input[name=productos]').attr('params');
        var subfamilias = $('input[name=subfamilias]').attr('params');
        var marcas = $('input[name=marcas]').attr('params');
        var ordenNro = $('input[name=ordenNro]').attr('params');
        var idSucursal = <?php session_start(); echo json_encode($_SESSION['sucursalId'] ?? null); ?>;
        var ordenadoPor  = $('select[name=ordenadoPor]').val();

        $('input#error', 'div#modal.inicio').val('0');

        $('input.required', 'div#modal.inicio').each(function(){
            window.validateInputs($(this));
        });

        if($('input#error', 'div#modal.inicio').val() == '0'){
            $.colorbox.close();
            window.open("http://localhost/newFront/exportador/exportadorStock.php?accion=hacerAlta&idSucursal="+idSucursal+"&fechaHasta="+fechaHasta+"&fechaDesde="+fechaDesde+"&subfamilias="+subfamilias+"&marcas="+marcas+"&idProducto="+productos+"&idFamilia="+familia+"&ordenadoPor="+ordenadoPor, "_blank");
        }
    });
</script>
