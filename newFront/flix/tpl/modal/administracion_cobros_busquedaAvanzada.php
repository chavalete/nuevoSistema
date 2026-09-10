<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de almacenes</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 70px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->          
    </div>
    <div class="modal_content">
        <input type="hidden" name="tipo" value="cobros" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Vendedor</span></td>
                <td class="field-input"><input type="text" name="vendedores" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Forma de Pago</span></td>
                <td class="field-input"><input type="text" name="formas" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Nro Factura</span></td>
                <td class="field-input"><input type="text" name="facturas" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker"/></td>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

