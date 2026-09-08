<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada</span>
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
        <input type="hidden" name="tipo" value="rentabilidad" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subfamilia</span></td>
                <td class="field-input"><input type="text" name="subfamilias" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Familia</span></td>
                <td class="field-input"><input type="text" name="familias" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Marca</span></td>
                <td class="field-input"><input type="text" name="marcas" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker required"/></td>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

