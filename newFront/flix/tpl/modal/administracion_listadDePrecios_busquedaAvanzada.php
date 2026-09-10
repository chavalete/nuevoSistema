<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de Listas de precio</span>
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
        <input type="hidden" name="tipo" value="listaDePrecios" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete"/></td>
            </tr>           
            <tr>
                <td class="field-name"><span class="field-name">Producto Nombre </span></td>
                <td class="field-input"><input type="text" name="nombre" /></td>
	    </tr>
	    <tr>
                <td class="field-name"><span class="field-name">Producto Familia</span></td>
                <td class="field-input"><input type="text" name="familias" class="autocomplete"/></td>
            </tr> 
            <tr>
                <td class="field-name"><span class="field-name">Vista</span></td>
                    <td class="field-input">
                        <select name="tipoVisa">
                            <option value="1">Detalle</option>
                            <option value="2">Admin</option>
                        </select>
                    </td>
                </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

