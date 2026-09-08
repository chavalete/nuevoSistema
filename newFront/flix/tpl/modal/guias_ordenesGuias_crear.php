<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Crear orden</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="crear" style="top: 30px; left: -300px; width: 320px; height: 120px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->            
    </div>
    <div class="modal_content">
        <table id="abmCrear">
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Tipo orden</span></td>
                <td class="field-input"><input type="text" name="cuentas" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Relacion Nombre</span></td>
                <td class="field-input"><input type="text" name="relaciones" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Paciente</span></td>
                <td class="field-input"><input type="text" name="pacientes" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Maxilar Sup.</span></td>
                <td class="field-input">
                    <select name="maxSup">
                        <option value="0">Seleccionar</option>
                        <option value="Slim Clear">Slim Clear</option>
                        <option value="Invisible por (vestibular )">Invisible por (vestibular )</option>
                        <option value="Invisible por( lingual )">Invisible por( lingual )</option>
                        <option value="Slimclip KID con hook">Slimclip KID con hook</option>
                        <option value="Lingual Clip con hook">Lingual Clip con hook</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Maxilar Inf.</span></td>
                <td class="field-input">
                    <select name="maxInf">
                        <option value="0">Seleccionar</option>
                        <option value="Slim Clear">Slim Clear</option>
                        <option value="Invisible por (vestibular )">Invisible por (vestibular )</option>
                        <option value="Invisible por( lingual )">Invisible por( lingual )</option>
                        <option value="Slimclip KID con hook">Slimclip KID con hook</option>
                        <option value="Lingual Clip con hook">Lingual Clip con hook</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Botones</span></td>
                <td class="field-input">
                    <select name="botones">
                        <option value="0">Seleccionar</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                </td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Arcos</span></td>
                <td class="field-input">
                    <select name="arcos">
                        <option value="0">Seleccionar</option>
                        <option value="Polimeros">Polimeros</option>
                        <option value="Soft Estetico">Soft Estetico</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fotos.</span></td>
                <td class="field-input">
                    <select name="fotos">
                        <option value="0">Seleccionar</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                        <option value="No">Pendiente</option>
                    </select>
                </td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">STL</span></td>
                <td class="field-input">
                    <select name="STL">
                        <option value="0">Seleccionar</option>
                        <option value="DIGITAL">DIGITAL</option>
                        <option value="Modelos">Modelos</option>
                        <option value="No">Pendiente</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Estudios Med.</span></td>
                <td class="field-input">
                    <select name="estudios">
                        <option value="0">Seleccionar</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                        <option value="No">Pendiente</option>
                    </select>
                </td>
            </tr>            
            <tr>
                <td class="field-name"><span class="field-name">Observaciones</span></td>
                <td class="field-input"><textarea type="text" name="observaciones"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button abmCrear">Crear</div>
    </div>
</div>

<script type="text/javascript">
    $('div.abmCrear').click(function(){
        window.rsAbm = '';
        if(!abmValidar()){
            $.ajaxSetup({async: false});
            abmCrear();
            $.ajaxSetup({async: true});
            $.msgBox({ title: "Alerta", content: window.rsAbm.mensaje});
            if(!window.rsAbm.soyError){
                $.colorbox.close();
                window.loadFlix();
            }
        }
    });

    var abmValidar = function(){
        $('input#error', 'div#modal.inicio').val('0');
        $('input.required', 'table#abmCrear').each(function(){
            window.validateInputs($(this));
        });     
        if($('input#error', 'div#modal.inicio').val() == '0'){
            return false;
        }else{
            return true;
        }
    }
    
    var abmCrear = function(){
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
                    relacionId    : $('input[name=relaciones]', 'table#abmCrear').attr('params'),
                    cuentas    : $('input[name=cuentas]', 'table#abmCrear').attr('params'),
                    pacienteId    : $('input[name=pacientes]', 'table#abmCrear').attr('params'),
                    maxSup  : $('select[name=maxSup]').val(),
                    maxInf  : $('select[name=maxInf]').val(),
                    botones  : $('select[name=botones]').val(),
                    arcos  : $('select[name=arcos]').val(),
                    fotos  : $('select[name=fotos]').val(),
                    stl  : $('select[name=STL]').val(),
                    estudios  : $('select[name=estudios]').val(),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'ordenes'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
