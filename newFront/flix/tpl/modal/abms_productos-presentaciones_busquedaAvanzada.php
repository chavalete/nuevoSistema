<div id="modal">
    <div class="modal_title">
        <span class="title">B&uacute;squeda avanzada de relaciones</span>
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
        <input type="hidden" name="tipo" value="productosPresentaciones" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Productos</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Nro. Gtin</span></td>
                <td class="field-input"><input type="text" name="gtin"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

