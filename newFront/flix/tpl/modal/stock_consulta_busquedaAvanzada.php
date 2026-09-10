<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada stock</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 300px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->          
    </div>
    <div class="modal_content">
        <form class="modal">
            <input type="hidden" name="ordenarPor" value="producto_id" />
            <input type="hidden" name="tipo" value="consultaStock" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Producto</span></td>
                    <td class="field-input"><input name="productos" class="autocomplete" type="text" /></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Subfamilia</span></td>
                    <td class="field-input"><input name="subfamilias" class="autocomplete" type="text" /></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Familia</span></td>
                    <td class="field-input"><input type="text" name="familias" class="autocomplete  parcial required"/></td>
                    <td class="addButton"></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Codigo Refencia</span></td>
                    <td class="field-input"><input name="codigoReferencia" type="text" /></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Marca</span></td>
                    <td class="field-input"><input name="marcas" class="autocomplete" type="text" /></td>
                </tr>            
            </table>            
        </form>

        <hr class="modal-divisor" />

        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>
