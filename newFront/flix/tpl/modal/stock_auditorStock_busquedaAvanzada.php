<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 325px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->        
    </div>
    <div class="modal_content">
        <table>
            <input type="hidden" name="tipo" value="auditorStock" />
            <tr>
                <td class="field-name"><span class="field-name">Producto</span></td>
                <td class="field-input"><input type="text" name="productos" class="autocomplete required" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Codigo de Trazabilidad</span></td>
                <td class="field-input"><input type="text" name="codigosAuditor"/></td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Almacen</span></td>
                <td class="field-input"><input type="text" name="almacenes" class="autocomplete"/></td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Estanteria</span></td>
                <td class="field-input"><input type="text" name="estanterias" class="autocomplete"/></td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Lote</span></td>
                <td class="field-input"><input type="text" name="lotes" class="autocomplete"/></td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Fecha desde</span></td>
                <td class="field-input"><input type="text" name="fechaDesde" class="datePicker"/></td>
            </tr>                        
            <tr>
                <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                <td class="field-input"><input type="text" name="fechaHasta" class="datePicker"/></td>
            </tr>                
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>