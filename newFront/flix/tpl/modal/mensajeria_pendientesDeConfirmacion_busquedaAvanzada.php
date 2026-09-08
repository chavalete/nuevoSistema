<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de Pendientes</span>
        <div class="tooltip">
            <div class="tooltip-position">
                <img src="img/ayuda.png" class="tooltip-click">
                <div style="top: 30px; left: -300px; width: 320px; height: 365px;" params="busquedaAvanzada" class="tooltip-content"></div>
            </div>
        </div>        
    </div>
    <div class="modal_content">
        <form class="modal">
            <input type="hidden" name="tipo" value="pendientesDeConfirmacion" />
            
            <table>
               <tr>
                    <td class="field-name"><span class="field-name">Nro. Gln Origen</span></td>
                    <td class="field-input"><input type="text" name="gln_origen" /></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nombre Gln Origen</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="origenNombre"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Gln Destino</span></td>
                    <td class="field-input"><input type="text" name="gln_destino" /></td>
                </tr>            

                <tr>
                    <td class="field-name"><span class="field-name">GTIN</span></td>
                    <td class="field-input"><input type="text" name="gtin"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Lote</span></td>
                    <td class="field-input"><input type="text" name="lote"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Traza</span></td>
                    <td class="field-input"><input type="text" name="numero_serial"/></td>
                </tr>
		 <tr>
                    <td class="field-name"><span class="field-name">ID transacción</span></td>
                    <td class="field-input"><input type="text" name="idTransaccionGlobal"/></td>
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
                    <td class="field-name"><span class="field-name">Nombre producto</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="nombreProducto"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Alertadas</span></td>
                    <td class="field-input">
                        <select name="alertadas">
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

