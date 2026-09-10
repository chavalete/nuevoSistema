<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de pedidos</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 365px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->        
    </div>
    <div class="modal_content">
        <form class="modal">
            <input type="hidden" name="tipo" value="listadoDePendientes" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Movimiento Nro. </span></td>
                    <td class="field-input"><input type="text" name="idMovimiento"/></td>
                </tr> 
                <tr>
                    <td class="field-name"><span class="field-name">Clientes</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="clientes"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Custodios</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="custodiantes"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Producto</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="productos"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Codigo Referencia</span></td>
                    <td class="field-input"><input type="text" name="codigoReferencia"/></td>
                </tr>            
                <tr>
                    <td class="field-name"><span class="field-name">Medicos</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="medicos"/></td>
                </tr>                        
                <tr>
                    <td class="field-name"><span class="field-name">Pacientes</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="pacientes"/></td>
                </tr>                
                <tr>
                    <td class="field-name"><span class="field-name">Obra Social</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="obraSocial"/></td>
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
                    <td class="field-name"><span class="field-name">Nro. Nota de Carga</span></td>
                    <td class="field-input"><input type="text" name="nroNotaCarga"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Fecha desde</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaDesde" /></td>
                </tr>     
                <tr>
                    <td class="field-name"><span class="field-name">Fecha hasta</span></td>
                    <td class="field-input"><input type="text" class="datePicker" name="fechaHasta"/></td>
                </tr>                 
                <tr>
                    <td class="field-name"><span class="field-name">Pendientes</span></td>
                    <td class="field-input">
                        <select name="pendientesImprimir">
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

