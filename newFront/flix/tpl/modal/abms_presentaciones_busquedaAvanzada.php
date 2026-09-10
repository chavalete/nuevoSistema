<div id="modal">
    <div class="modal_title">
        <span class="title">B&uacute;squeda avanzada de presentaciones</span>
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
        <input type="hidden" name="tipo" value="presentaciones" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Presentacion</span></td>
                <td class="field-input"><input type="text" name="presentaciones" class="autocomplete"/></td>
            </tr>
            <tr>
		<td class="field-name"><span class="field-name">Cancelada</span></td>
		<td class="field-input">
		    <select name="cancelada">
			<option value="">Seleccionar</option>
			<option value="1">Si</option>
			<option value="2">No</option>
		    </select>
		</td>
	    </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

