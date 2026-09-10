<div id="modal">
    <div class="modal_title">
        <span class="title">B&uacute;squeda avanzada de productos</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 150px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->              
    </div>
    <div class="modal_content">
        <input type="hidden" name="tipo" value="productos" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Productos</span></td>
                <td class="field-input"><input type="text" name="productosAbm" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Subfamilia Nombre</span></td>
                <td class="field-input"><input type="text" name="subfamilias" class="autocomplete"/></td>
            </tr>    
            <tr>
                <td class="field-name"><span class="field-name">Familia</span></td>
                <td class="field-input"><input type="text" name="familias" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Codigo de Barras </span></td>
                <td class="field-input"><input type="text" name="gtin" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Proveedor</span></td>
                <td class="field-input"><input type="text" name="proveedores" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Marca</span></td>
                <td class="field-input"><input type="text" name="marcas" class="autocomplete"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

