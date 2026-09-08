<div id="modal">
    <div class="modal_title">
        <span class="title">Busqueda avanzada de pacientes</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="busquedaAvanzada" style="top: 30px; left: -300px; width: 320px; height: 50px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->          
    </div>
    <div class="modal_content">
        <input type="hidden" name="tipo" value="pacientes" />
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Nombre</span></td>
                <td class="field-input"><input type="text" name="pacientes" class="autocomplete"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button busquedaAvanzada">Buscar</div>
    </div>
</div>

