<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de guias</span>
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
            <input type="hidden" name="tipo" value="ordenes" />
            
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Cliente</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="clientes"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Relacion</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="relaciones"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Cuenta</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="cuentas"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Paciente</span></td>
                    <td class="field-input"><input type="text" class="autocomplete" name="pacientes"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Nro. Guia</span></td>
                    <td class="field-input"><input type="text" name="guiaNro"/></td>
                </tr>     
            </table>
        </form>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

