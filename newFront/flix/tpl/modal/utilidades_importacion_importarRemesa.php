<div id="modal">
    <div class="modal_title">
        <span class="title">Importar remesa</span>
        <!-- Tooltip -->
        <div class='tooltip'>
            <div class="tooltip-position">
                <img class="tooltip-click" src="img/ayuda.png" />
                <div class="tooltip-content" params="importarRemesa" style="top: 30px; left: -300px; width: 320px; height: 70px;"></div>
            </div>
        </div>
        <!-- Fin de la Tooltip -->           
    </div>
    <form class='FormularioRemesa' enctype="multipart/form-data" method="POST" onsubmit="importarRemesa()">
        <input type="hidden" name="accion" value="upload" />
        <input type="hidden" name="desdeAlta" id="desdeAlta" value="false" />
        <input type="hidden" name="tipo" id="tipo" value="importarRemesa" />

        <div class="modal_content">
            <table>
                <tr>
                    <td class="field-name"><span class="field-name">Proveedor</span></td>
                    <td class="field-input" style="padding: 7px;"><input type="text" class="autocomplete" name="proveedores"/></td>
                </tr>
                <tr>
                    <td class="field-name"><span class="field-name">Archivo</span></td>
                    <td class="field-input" style="padding: 7px;"> 
                        <input type="file" id="archivo" name="archivo" />
                    </td>
                </tr>            
            </table>
            <hr class="modal-divisor" />

            <div class="send-button importar">Importar</div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $('div.send-button.importar').click(function(){
        if(validateForm()){
            return false;
        }
        
        var inputFile   = document.getElementById("archivo");
        var file        = inputFile.files[0];
        var data        = new FormData();
        var url         = "inc/upload.php";

        data.append('archivo', file);

        $.ajax({
            url         : url,
            type        : 'POST',
            contentType : false,
            data        : data,
            processData : false,  
            cache       : false,
            dataType    : 'json',
            complete    : function(data){
                var response = JSON.parse(data.responseText);
                
                if(!response.error){
                    process(response.file);
                }else{
                    $.msgBox({ title: "Alerta", content: 'No se pudo subir el archivo', type: 'error'});
                }
            }
        });
    });
    var process = function(fileName){
        $.post(
            'inc/process.php',
            {   
                accion     : 'hacerAlta',
                parametros : {
                    proveedores : $("input[name=proveedores]").attr('params'),
                    archivo     : fileName,
                    tipo        : 'importarRemesa',
                    desdeAlta   : false
                }
            }, 
            function(data){
                borrarArchivoTemp(fileName);
                //var response = JSON.parse(data);
                
                $.msgBox({ title: "Informacion", content: data.mensaje});
                if(!data.soyError){
                    $.colorbox.close();
                    window.loadFlix();                    
                }
            },
            'json'
        );           
    }
    var validateForm = function(){
        var file = $('input[name=archivo]');
        var proveedor = $('input[name=proveedores]');
        var error = false;
        
        if(file.val()){
            file.removeClass('error');
        }else{
            file.addClass('error');
            error = true;
        }
        if(typeof proveedor.attr('params') == 'undefined' || proveedor.attr('params').trim() == ''){
            proveedor.addClass('error');
            error = true;
        }else{
            proveedor.removeClass('error');
        }
        return error;
    }
    var borrarArchivoTemp = function(fileName){
        $.post(
            'inc/deleteTemp.php',
            {   
                accion : 'borrar',
                file   : fileName
            }, 
            function(){}
        );           
    }
</script>