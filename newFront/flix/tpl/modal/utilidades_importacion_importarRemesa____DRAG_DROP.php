<div id="modal">
    <div class="modal_title">
        <span class="title">Importar remesa</span>
    </div>
    <div class="modal_content">
        <table>
            <tr>
                <td class="field-name"><span class="field-name">Proveedor</span></td>
                <td class="field-input" style="padding: 7px;"><input type="text" /></td>
            </tr>
            <tr>
                <td class="field-name"><span class="field-name">Archivo</span></td>
                <td class="field-input" style="padding: 7px;"> 
                    <div id="drop_zone" style="border: 2px dashed #BBBBBB; border-radius: 5px 5px 5px 5px; color: #BBBBBB; font: 20pt bold,'Vollkorn'; padding: 25px; text-align: center;">
                        Arrastrar aqui
                    </div>
                    <output id="list"></output>
                </td>
            </tr>            
        </table>
        <hr class="modal-divisor" />

        <div class="send-button importar">Crear</div>
    </div>
</div>

<script type="text/javascript">
    // Check for the various File API support.
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        // Great success! All the File APIs are supported.
    } else {
        alert('The File APIs are not fully supported in this browser.');
    }
    var originalSize = Number($('div#cboxWrapper').css('height').slice(0, -2));

    function handleFileSelect(evt, originalSize) {
        evt.stopPropagation();
        evt.preventDefault();

        var files = evt.dataTransfer.files; // FileList object.

        // files is a FileList of File objects. List some properties.
        var output = [];
        for (var i = 0, f; f = files[i]; i++) {
            output.push(escape(f.name));
        }
        document.getElementById('list').innerHTML = output.join('');
                
        var resize = originalSize + Number($('output#list').css('height').slice(0, -2)) + 5;
        $.colorbox.resize({height: resize});
    }

    function handleDragOver(evt) {
        evt.stopPropagation();
        evt.preventDefault();
        evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
    }

    // Setup the dnd listeners.
    var dropZone = document.getElementById('drop_zone');
    dropZone.addEventListener('dragover', handleDragOver, false);
    dropZone.addEventListener('drop', handleFileSelect, false);
</script>