<div id="modal">
    <div class="modal_title">
        <span class="title">Búsqueda avanzada de clientes</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: auto;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->        
    </div>
    <div class="modal_content">
        <input type="hidden" name="tipo" value="clientes" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Clientes</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Categorias</span></td>
                <td class="field-input"><input type="text" name="categorias" class="autocomplete" /></td>
            </tr>
            <tr>
	    <td class="field-name"><span class="field-name">Nro. Cuit</span></td>
	    <td class="field-input"><input type="text" name="cuit" /></td>
	</tr>
	<tr>
	    <td class="field-name"><span class="field-name">Vendedor</span></td>
	    <td class="field-input"><input type="text" name="vendedores" class="autocomplete"/></td>
	</tr>
	<tr>
	    <td class="field-name"><span class="field-name">Posicion IVA</span></td>
	    <td class="field-input">
		<select name="iva">
		    <option value="f">Exento</option>
		    <option value="t">Responsable Inscripto</option>
		</select>
	    </td>
	</tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>