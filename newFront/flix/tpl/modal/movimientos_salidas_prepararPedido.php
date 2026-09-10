<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Preparar pedido</span>
        <!-- Tooltip 
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="salidaStock" style="top: 30px; left: -300px; width: 320px; height: 300px;"></div>
            </div>
        </div>
        <Fin de la Tooltip -->    
    </div>
    
    <div class="modal_content">
        <table>
            <input id="error" type="hidden" value="0" />
            <tr>
                <td class="primary">
                    <table>
                        <tr>
                            <td class="field-name"><span class="field-name">Codigo</span></td>
                            <td class="field-input">
                                <form class="pedido" onsubmit="return false;"> 
                                    <input type="text" name="codigos" id="codigoPedido"/>
                                </form>
                            </td>
                        </tr>
                    </table>
                </td>
                
                <td class="secundary" style="display: none; width: 360px; vertical-align: top;"></td>                 
            </tr> 
        </table>                        
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        var valCodigo = function(){
            return true;
        }
        
        $('input[name=codigos]', 'div.inicio').keypress(function(e){
            var keyCode = e.which;
            var codigo  = $(this).val();
            
            if(keyCode == 13){ // keyCode 13 es ENTER
                if(valCodigo(codigo)){
                    var params = {
                        accion      : 'prepararPedido',
                        parametros  : { codigo : codigo }
                    };
                    $.ajax({
                        url     : 'inc/process.php',
                        data    : params,
                        type    : 'POST',
                        dataType: 'json'
                    }).done(function(data){
                        $.msgBox({ title: "Error", content: data.mensaje, type: 'error' });
                    }).fail(function(){
                        $.colorbox({
                            href    : "tpl/modal/movimientos_salidas_prepararPedido_step2.php",
                            data    : params,
                            width   : '1200',
                            height  : 'auto'
                        });                        
                    });                    
                }else{
                    $.msgBox({ title: "Alerta", content: "Código invalido" });
                    $('input.msgButton', 'div.msgBoxButtons').focus();
                    $(this).val(''); 
                }
            }
        });
        setTimeout(function(){
            $('#codigoPedido').focus()
        }, 500);
        
        
    });    
</script>
