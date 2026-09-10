<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada</span>
        <!-- Comienza la Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 150px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->         
    </div>
    <div class="modal_content">
        <form class="modal">
            <input type="hidden" name="tipo" value="bloqueador" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Producto</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="nombreProducto"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Lote</span></td>
                    <td class="field-input"><input type="text" name="lote"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Codigo Trazabilidad</span></td>
                    <td class="field-input"><input type="text" name="codigo"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nombre Gln Origen</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="origenNombre" /></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Referencia</span></td>
                    <td class="field-input"><input type="text" name="nroReferencia"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Remito</span></td>
                    <td class="field-input"><input type="text" name="remitos"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Factura</span></td>
                    <td class="field-input"><input type="text" name="facturas"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Sucursal</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="sucursalMensajeria" /></td>
                </tr>    
                <tr>
                    <td class="field-name"><span class="field-name">Bloqueadas</span></td>
                    <td class="field-input">
                        <select name="bloqueadas">
                            <option value="">Seleccionar</option>
                            <option value="2">Si</option>
                            <option value="1">No</option>
                        </select>
                    </td>
                </tr>                            
            </table>
        </form>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

