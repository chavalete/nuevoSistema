<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Diferencia de precio</span>
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
                <td class="field-name"><span class="field-name">Remito</span></td>
                <td class="field-input"><input type="text" name="facturas" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Importe</span></td>
                <td class="field-input"><input class="required" type="text" name="importe"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Observaciones</span></td>
                <td class="field-input"><textarea type="text" name="observaciones"/></td>
            </tr>
        </table>
        <hr class="modal-divisor" />
        
        <div class="send-button abmCrear">Devolver</div>
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
                    importe     : $('input[name=importe]', 'table#abmCrear').val(),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    remito    : $('input[name=facturas]', 'table#abmCrear').attr('params'),
                    tipo          : 'diferenciaDePrecio'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
