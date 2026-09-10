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
            <input type="hidden" name="tipo" value="ordenDeTrabajo" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Producto</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="productos"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Nro. de Orden</span></td>
                    <td class="field-input"><input type="text" name="nroOrden"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Estado</span></td>
                    <td class="field-input"><input type="text" name="estados" class="autocomplete"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Fecha desde</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaDesde" /></td>
                </tr>     
                <tr>
                    <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaHasta"/></td>
                </tr>                 
            </table>
        </form>
        <hr class="modal-divisor" />
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

