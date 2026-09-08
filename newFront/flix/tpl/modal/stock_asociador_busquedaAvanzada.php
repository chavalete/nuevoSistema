<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada</span>
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
            <input type="hidden" name="tipo" value="asociadorStock" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Producto</span></td>
                    <td class="field-input"><input name="productos" class="autocomplete" type="text" /></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Asociado</span></td>
                    <td class="field-input">
                        <select name="asociado">
                            <option value="">Seleccionar</option>
                            <option value="1">Si</option>
                            <option value="2">No</option>
                        </select>
                    </td>
                </tr>
            </table>            
        </form>

        <hr class="modal-divisor" />

        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>
