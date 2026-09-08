<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Alta de relacion clientes->cuentas</span>
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
                <td class="field-name"><span class="field-name">Relacion Nombre</span></td>
                <td class="field-input"><input class="required" type="text" name="relacion"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Cliente</span></td>
                <td class="field-input"><input type="text" name="clientes" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Cuenta</span></td>
                <td class="field-input"><input type="text" name="cuentas" class="autocomplete required"/></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Vendedor</span></td>
                <td class="field-input"><input type="text" name="vendedores" class="autocomplete required"/></td>
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
                    relacionNombre     : $('input[name=relacion]', 'table#abmCrear').val(),
                    clientes    : $('input[name=clientes]', 'table#abmCrear').attr('params'),
                    vendedores    : $('input[name=vendedores]', 'table#abmCrear').attr('params'),
                    cuentas    : $('input[name=cuentas]', 'table#abmCrear').attr('params'),
                    desdeAlta     : false,
                    observaciones : $('textarea[name=observaciones]', 'table#abmCrear').val(),
                    tipo          : 'relaciones'
                }
            }, 
            function(data){
                window.rsAbm = data;
            },
            'json'
        );        
    }
</script>
