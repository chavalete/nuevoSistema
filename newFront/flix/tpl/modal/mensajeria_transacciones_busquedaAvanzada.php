<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de transacciones</span>
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
            <input type="hidden" name="tipo" value="transacciones" />
            
            <table> 				            
                <tr>
                    <td class="field-name"><span class="field-name">Nombre producto</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="nombreProducto"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Lote</span></td>
                    <td class="field-input"><input type="text" name="lote"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Codigo</span></td>
                    <td class="field-input"><input type="text" name="codigo"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Gln Origen</span></td>
                    <td class="field-input"><input type="text" name="origen" /></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nombre Gln Origen</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="origenNombre"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nombre Gln Destino</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="destinoNombre"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Gln Destino</span></td>
                    <td class="field-input"><input type="text" name="destino"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Tipo Movimiento</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="tipoMovimientoMensajeria"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Referencia</span></td>
                    <td class="field-input"><input type="text" name="nroReferencia"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Remito</span></td>
                    <td class="field-input"><input type="text" name="remito"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro Factura</span></td>
                    <td class="field-input"><input type="text" name="facturas"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Fecha desde</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaDesde"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaHasta"/></td>
                </tr>
                
                <tr>
                    <td class="field-name"><span class="field-name">Requiere Autogestion</span></td>
                    <td class="field-input">
                        <select name="tipoMovimientoId">
                            <option value="">Seleccionar</option>
                            <option value="1">Si</option>
                            <option value="2">No</option>
                            <option value="3">Cancelada</option>
                        </select>
                    </td>
                </tr>    
                <tr>
                    <td class="field-name"><span class="field-name">Sucursal</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="sucursalMensajeria" /></td>
                </tr>                            
            </table>
        </form>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

