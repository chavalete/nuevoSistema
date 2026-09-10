<div id="modal">
    <div class="modal_title">
        <span class="title">Búsqueda avanzada de proveedores</span>
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
        <input type="hidden" name="tipo" value="proveedores" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Proveedor</span></td>
                <td class="field-input"><input type="text" name="proveedores" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Categorias</span></td>
                <td class="field-input"><input type="text" name="categorias" class="autocomplete"/></td>
            </tr>
            <tr>
		<td class="field-name"><span class="field-name">Nro. CUIT</span></td>
		<td class="field-input"><input type="text" name="proveCuit"/></td>
	    </tr>
	    <tr>
		<td class="field-name"><span class="field-name">Nro. GLN</span></td>
		<td class="field-input"><input type="text" name="gln"/></td>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

