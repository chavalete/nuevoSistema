<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de Liquidaciones</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="crear" style="top: 30px; left: -300px; width: 320px; height: 90px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->           
    </div>
    <div class="modal_content">
        <table id="abmCrear">
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="field-name"><span class="field-name">Vendedor</span></td>
                <td class="field-input"><input class="autocomplete" type="text" name="vendedoresClientes" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Fecha Desde</span></td>
                <td class="field-input"><input type="text" class="datePicker" name="fechaDesde" /></td>
            </tr> 
            <tr>
                <td class="field-name"><span class="field-name">Fecha Hasta</span></td>
                <td class="field-input"><input type="text" class="datePicker" name="fechaHasta" /></td>
            </tr> 
            <tr>
                <td class="field-name"><span class="field-name">Observaciones</span></td>
                <td class="field-input"><textarea type="text" name="observaciones" /></td>
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
            //console.log(window.rsAbm.soyError);
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
                    vendedorId       : $('input[name=vendedoresClientes]').attr('params'),
                    fechaDesde    : $('input[name=fechaDesde]', 'table#abmCrear').val(),
                    fechaHasta    : $('input[name=fechaHasta]', 'table#abmCrear').val(),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'liquidaciones'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
