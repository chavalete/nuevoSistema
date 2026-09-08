<?php 
include('../../inc/process.php'); 

//echo $data['soyError'];


?>

<div id="modal" class="inicio">
    <div class="modal_title">
        <span class="title">Detalles</span>
        
        <!-- Tooltip 
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="<?=$helimonMorenoParams;?>" style="top: 30px; left: -300px; width: 320px; height: 325px;"></div>
            </div>
        </div>
        Fin de la Tooltip -->         
    </div>
    
    <div class="detalles-cabecera">
        <table width="100%">
            <?php for($i=0; $i<count($data['propiedades']); $i++){ ?>
                <tr>
                    <td class="cabecera-title"><?=$data['propiedades'][$i]['display']?></td>
                    <td class="cabecera-detalle">
                        <div class="cabecera-dato"><?=$data['propiedades'][$i]['value']?></div>
                    </td>
                </tr>
            <?php } ?>
                <tr>
                    <td class="cabecera-title">
                        <form onsubmit="return false;">
                            <input class="addCodigo" />
                        </form>
                    </td>
                    <td class="cabecera-detalle" style="height: 100px;">
                        <div class="cabecera-dato codigoAdded" style="overflow: auto; max-height: 100px; height: 100px;"></div>
                    </td>                    
                </tr>
        </table>
    </div>

    <div class="detalles-table">
        <table width="100%">
            <thead>
                <tr>
                    <?php if(!empty($data['alertar'])){ ?>
                        <th class="detalles-titulo"><input type="checkbox" name="selectAll" style="width: 13px;"/></th>
                    <?php } ?>
                    <?php for($i=0; $i<count($data['listadoDetalles']['modelo']); $i++){ ?>
                        <th class="detalles-titulo"><?=$data['listadoDetalles']['modelo'][$i]['display']?></td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php for($i=0; $i<count($data['listadoDetalles']['celdas']); $i++){ 
                    $class = ($i%2) ? 'impar' : 'par';
                    ?>
                    <tr class="detalles-dato <?=$class?>" id="<?=$data['listadoDetalles']['celdas'][$i]['id'];?>">
                        <?php if(!empty($data['alertar'])){ ?>
                            <td class="detalles-titulo" style="width: 13px; padding-left: 0; text-align: center;">
                                <?php if(empty($data['listadoDetalles']['celdas'][$i]['alertado'])){ ?>
                                    <input type="checkbox" name="alertar" style="width: 13px;"/>
                                <?php } ?>
                            </td>
                        <?php } ?>
                        
                        <?php for($e=0; $e<count($data['listadoDetalles']['celdas'][$i]['cell']); $e++){ ?>
                            <td>
                                <div class="detalle-dato"><?=$data['listadoDetalles']['celdas'][$i]['cell'][$e];?></div>
                            </td>
                        <?php } ?>
                      
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>    
    <hr>
    <div class="send-button save" style="margin-right: 170px; display: none;">Guardar</div>
    <div class="send-button" onclick="Javascript: $.colorbox.close();">Cerrar</div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
        var valCodigo = function(codigo){
            return true;
        }
        
        $('input.addCodigo', 'div.inicio').keypress(function(e){
            var keyCode = e.which;
            var codigo  = $(this).val();

            if(keyCode == 13){ // keyCode 13 es 
                if(valCodigo(codigo)){
                    window.callService('lectorCodigo', { codigo: codigo });
                    if(typeof(window.rsService.soyError) == 'undefined' || window.rsService.soyError){
                        $.msgBox({ title: "Error", content: "El código \""+ codigo +"\" es inexistente" });
                        $('input.msgButton', 'div.msgBoxButtons').focus();
                    }else{
                        $('<span class="codigo" codigo="'+window.rsService.valor+'">'+window.rsService.descripcion+'<span class="codigoBorrar">x</span></span>').appendTo('div.codigoAdded');
                        $('div.cabecera-dato.codigoAdded').animate({scrollTop: $('div.cabecera-dato.codigoAdded').height()}, 1000);
                        $('.send-button.save').show();
                    }
                }else{
                    $.msgBox({ title: "Alerta", content: "Código invalido" });
                }
                $(this).val(''); 
            }
        });
        
        $('span.codigoBorrar').live('click', function(){
            $(this).closest('span.codigo').remove();
            if($('.cabecera-dato.codigoAdded > span').length < 1){
                $('.send-button.save').hide();
            }
        });
        
        $('.send-button.save').click(function(){
            if($('.cabecera-dato.codigoAdded > span').length == 0){
                $.msgBox({ title: "Alerta", content: "Debe agregar al menos un producto para poder guardar el pedido." });
            }else{
                var codes = '';
                $.each($('.cabecera-dato.codigoAdded > span'), function(){
                    codes += $(this).attr('codigo') + '||';
                })
                
                window.callService(
                        'validarOrden', 
                        {
                            id      : '<?=$_REQUEST['parametros']['codigo'];?>',
                            codigos : codes,
                            tipo    : window.parametros.tipo
                        }
                );
                var table = '';
                if(typeof window.rsService.tabla !== 'undefined'){
					table += '<table id="rsServiceTable" style="border-collapse: collapse; margin-top: 5px;">';
					table += '<tr>';
					for(var i = 0; i<window.rsService.tabla.titulo.length; i++){
					    table += '<th style="border: 1px solid black; background-color: #D9EBF5;">' + window.rsService.tabla.titulo[i] + '</th>';
					}
					table += '</tr>';
	                
					$.each(window.rsService.tabla.datos, function(){
						table += '<tr>';
					    var mthis = this;
					    $.each(mthis, function(key, value){
					        if(key !== 'color' ){
						        table += '<td style="color: '+ mthis.color +'; text-align: center; padding: 0 10px; border: 1px solid black;">' + value + '</td>';	
						    }
					    });
						table += '</tr>';
					});

					table += '</table>';	                
                }              
                
                if(window.rsService.soyError == true){
                    $.msgBox({ title: "error", content: window.rsService.mensaje + table});
                }else{
                    $.msgBox({ title: "info", content: window.rsService.mensaje + table});
                    loadFlix();
                    $.colorbox.close();                    
                }
                $('table#rsServiceTable').siblings().hide();
				$('table#rsServiceTable').closest('div').css('height', $('table#rsServiceTable').css('height'));
            }
        });
        setTimeout(function(){
            $('input.addCodigo').focus()
        }, 500);        
  });
</script>
